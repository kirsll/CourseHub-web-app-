<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Enrollment;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    private const DB_MODE_PROTECTED_COLUMNS = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'password',
    ];

    private function getDbTables(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        $tables = [];
        if ($driver === 'mysql') {
            $rows = $connection->select('SHOW TABLES');
            foreach ($rows as $row) {
                $tables[] = array_values((array) $row)[0];
            }
        } elseif ($driver === 'pgsql') {
            $rows = $connection->select("select tablename from pg_tables where schemaname = 'public'");
            foreach ($rows as $row) {
                $tables[] = $row->tablename;
            }
        } elseif ($driver === 'sqlite') {
            $rows = $connection->select("select name from sqlite_master where type='table' and name not like 'sqlite_%'");
            foreach ($rows as $row) {
                $tables[] = $row->name;
            }
        }

        return collect($tables)
            ->filter(fn ($t) => is_string($t) && $t !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function ensureValidDbTable(string $table): void
    {
        if (!in_array($table, $this->getDbTables(), true)) {
            abort(404);
        }
    }

    private function isDbTableEditable(string $table): bool
    {
        $columns = Schema::getColumnListing($table);

        return in_array('id', $columns, true);
    }

    private function getDbEditableColumns(string $table): array
    {
        $columns = Schema::getColumnListing($table);

        return collect($columns)
            ->reject(fn ($c) => in_array($c, self::DB_MODE_PROTECTED_COLUMNS, true))
            ->values()
            ->all();
    }

    private function getDbFieldDescriptions(string $table): array
    {
        // Базовые описания, которые подходят многим таблицам
        $common = [
            'is_active' => '1 - Активен, 0 - Неактивен',
            'is_published' => '1 - Опубликован, 0 - Черновик',
            'is_featured' => '1 - Рекомендованный, 0 - Обычный',
            'sort_order' => 'Порядок сортировки (чем меньше число, тем выше)',
            'slug' => 'ЧПУ (URL-имя), только латиница, цифры и дефис',
            'user_id' => 'Пользователь, к которому привязана запись',
            'instructor_id' => 'Преподаватель курса',
            'category_id' => 'Категория',
            'course_id' => 'Курс',
            'module_id' => 'Модуль курса',
            'lesson_id' => 'Урок',
            'role_id' => 'Роль пользователя',
        ];

        // Специфичные для таблиц описания
        $specific = [];
        if ($table === 'courses') {
            $specific = [
                'level' => 'Уровень сложности',
                'language' => 'Язык курса (ru/en и т.д.)',
                'price' => 'Стоимость в рублях',
                'discount_price' => 'Цена со скидкой (оставьте пустым, если скидки нет)',
            ];
        } elseif ($table === 'orders' || $table === 'payments') {
            $specific = [
                'status' => 'Текущий статус',
                'amount' => 'Сумма',
            ];
        }

        return array_merge($common, $specific);
    }

    private function getDbFieldOptions(string $table, array $columns): array
    {
        $options = [];

        // Общие булевы значения
        foreach (['is_active', 'is_published', 'is_featured', 'is_free_preview'] as $boolField) {
            if (in_array($boolField, $columns, true)) {
                $options[$boolField] = [0 => 'Нет', 1 => 'Да'];
            }
        }

        // Связи (внешние ключи)
        if (in_array('role_id', $columns, true)) {
            $options['role_id'] = \App\Models\Role::pluck('name', 'id')->toArray();
        }
        if (in_array('user_id', $columns, true) || in_array('instructor_id', $columns, true)) {
            // Для пользователей берем email как идентификатор для списка
            $users = \App\Models\User::select('id', 'first_name', 'last_name', 'email')->get()
                ->mapWithKeys(fn($u) => [$u->id => "{$u->first_name} {$u->last_name} ({$u->email})"])->toArray();
            
            if (in_array('user_id', $columns, true)) $options['user_id'] = $users;
            if (in_array('instructor_id', $columns, true)) $options['instructor_id'] = $users;
        }
        if (in_array('category_id', $columns, true) || in_array('parent_id', $columns, true)) {
            $categories = \App\Models\Category::pluck('name', 'id')->toArray();
            if (in_array('category_id', $columns, true)) $options['category_id'] = $categories;
            if (in_array('parent_id', $columns, true)) {
                $options['parent_id'] = [null => 'Нет (Главная категория)'] + $categories;
            }
        }
        if (in_array('course_id', $columns, true)) {
            $options['course_id'] = \App\Models\Course::pluck('title', 'id')->toArray();
        }
        if (in_array('module_id', $columns, true)) {
            $options['module_id'] = \Illuminate\Support\Facades\DB::table('modules')->pluck('title', 'id')->toArray();
        }
        if (in_array('lesson_id', $columns, true)) {
            $options['lesson_id'] = \Illuminate\Support\Facades\DB::table('lessons')->pluck('title', 'id')->toArray();
        }
        if (in_array('order_id', $columns, true)) {
            $options['order_id'] = \App\Models\Order::pluck('id', 'id')->toArray();
        }

        // Enum / статусы
        if ($table === 'courses' && in_array('level', $columns, true)) {
            $options['level'] = [
                'beginner' => 'Начальный',
                'intermediate' => 'Средний',
                'advanced' => 'Продвинутый',
            ];
        }
        if ($table === 'courses' && in_array('language', $columns, true)) {
            $options['language'] = [
                'ru' => 'Русский',
                'en' => 'Английский',
            ];
        }
        if ($table === 'lessons' && in_array('type', $columns, true)) {
            $options['type'] = [
                'video' => 'Видео',
                'text' => 'Текст',
                'quiz' => 'Тест',
                'document' => 'Документ',
            ];
        }
        if ($table === 'orders' && in_array('status', $columns, true)) {
            $options['status'] = [
                'pending' => 'В ожидании',
                'paid' => 'Оплачен',
                'cancelled' => 'Отменен',
                'refunded' => 'Возвращен',
            ];
        }
        if ($table === 'payments' && in_array('status', $columns, true)) {
            $options['status'] = [
                'pending' => 'В ожидании',
                'completed' => 'Завершен',
                'failed' => 'Ошибка',
                'refunded' => 'Возвращен',
            ];
        }

        return $options;
    }

    private function getDbWritableData(Request $request, string $table): array
    {
        $editable = $this->getDbEditableColumns($table);

        $data = [];
        foreach ($editable as $col) {
            if ($request->has($col)) {
                $value = $request->input($col);
                if ($value === '') {
                    $value = null;
                }
                $data[$col] = $value;
            }
        }

        return $data;
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::whereHas('role', function ($q) {
                $q->where('name', 'student');
            })->count(),
            'total_instructors' => User::whereHas('role', function ($q) {
                $q->where('name', 'instructor');
            })->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::published()->count(),
            'total_enrollments' => Enrollment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->where('paid_at', '>=', now()->subMonth())
                ->sum('amount'),
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentCourses = Course::with('instructor')->orderBy('created_at', 'desc')->take(5)->get();
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentCourses',
            'recentOrders'
        ));
    }

    // Управление пользователями
    public function users()
    {
        $roles = Role::all();

        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users', 'roles'));
    }

    public function createUser()
    {
        $roles = Role::all();
        
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Пользователь создан');
    }

    public function editUser(User $user)
    {
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $user->update($request->all());

        if ($request->password) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('admin.users')
            ->with('success', 'Пользователь обновлен');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Нельзя удалить текущего пользователя');
        }

        $user->delete();

        return back()->with('success', 'Пользователь удален');
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update(['role_id' => $request->role_id]);

        return back()->with('success', 'Роль пользователя обновлена');
    }

    public function roles()
    {
        $roles = Role::orderBy('id')->get();

        return view('admin.roles', compact('roles'));
    }

    public function dbMode(Request $request)
    {
        $driver = DB::connection()->getDriverName();
        $tables = $this->getDbTables();

        $selectedTable = $request->query('table');
        if ($selectedTable && !in_array($selectedTable, $tables, true)) {
            $selectedTable = null;
        }
        if (!$selectedTable && count($tables) > 0) {
            $selectedTable = $tables[0];
        }

        $columns = [];
        $rows = null;
        $isEditable = false;
        if ($selectedTable) {
            $columns = Schema::getColumnListing($selectedTable);
            $isEditable = $this->isDbTableEditable($selectedTable);

            $orderColumn = null;
            if (in_array('id', $columns, true)) {
                $orderColumn = 'id';
            } elseif (in_array('created_at', $columns, true)) {
                $orderColumn = 'created_at';
            }

            $query = DB::table($selectedTable);
            if ($orderColumn) {
                $query->orderBy($orderColumn, 'desc');
            }

            $rows = $query->paginate(50)->appends(['table' => $selectedTable]);
        }

        return view('admin.db.index', [
            'driver' => $driver,
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'columns' => $columns,
            'rows' => $rows,
            'isEditable' => $isEditable,
        ]);
    }

    public function dbCreate(string $table)
    {
        $this->ensureValidDbTable($table);
        if (!$this->isDbTableEditable($table)) {
            return redirect()->route('admin.db', ['table' => $table])
                ->with('error', 'Эта таблица доступна только для просмотра');
        }

        $columns = $this->getDbEditableColumns($table);
        $descriptions = $this->getDbFieldDescriptions($table);
        $options = $this->getDbFieldOptions($table, $columns);

        return view('admin.db.create', compact('table', 'columns', 'descriptions', 'options'));
    }

    public function dbStore(Request $request, string $table)
    {
        $this->ensureValidDbTable($table);
        if (!$this->isDbTableEditable($table)) {
            return redirect()->route('admin.db', ['table' => $table])
                ->with('error', 'Эта таблица доступна только для просмотра');
        }

        $data = $this->getDbWritableData($request, $table);
        DB::table($table)->insert($data);

        return redirect()->route('admin.db', ['table' => $table])
            ->with('success', 'Запись добавлена');
    }

    public function dbEdit(string $table, int $id)
    {
        $this->ensureValidDbTable($table);
        if (!$this->isDbTableEditable($table)) {
            return redirect()->route('admin.db', ['table' => $table])
                ->with('error', 'Эта таблица доступна только для просмотра');
        }

        $columns = $this->getDbEditableColumns($table);
        $row = DB::table($table)->where('id', $id)->first();
        if (!$row) {
            abort(404);
        }

        $descriptions = $this->getDbFieldDescriptions($table);
        $options = $this->getDbFieldOptions($table, $columns);

        return view('admin.db.edit', compact('table', 'columns', 'row', 'id', 'descriptions', 'options'));
    }

    public function dbUpdate(Request $request, string $table, int $id)
    {
        $this->ensureValidDbTable($table);
        if (!$this->isDbTableEditable($table)) {
            return redirect()->route('admin.db', ['table' => $table])
                ->with('error', 'Эта таблица доступна только для просмотра');
        }

        $data = $this->getDbWritableData($request, $table);
        DB::table($table)->where('id', $id)->update($data);

        return redirect()->route('admin.db', ['table' => $table])
            ->with('success', 'Запись обновлена');
    }

    public function dbDestroy(string $table, int $id)
    {
        $this->ensureValidDbTable($table);
        if (!$this->isDbTableEditable($table)) {
            return redirect()->route('admin.db', ['table' => $table])
                ->with('error', 'Эта таблица доступна только для просмотра');
        }

        DB::table($table)->where('id', $id)->delete();

        return redirect()->route('admin.db', ['table' => $table])
            ->with('success', 'Запись удалена');
    }

    public function dbSchema()
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        $tables = $this->getDbTables();

        $tableInfo = [];
        $triggers = [];
        $routines = [];

        $relations = [];
        if ($driver === 'mysql') {
            $database = $connection->getDatabaseName();

            $columnRows = $connection->select(
                "SELECT TABLE_NAME as table_name, COLUMN_NAME as column_name, COLUMN_TYPE as column_type, IS_NULLABLE as is_nullable, COLUMN_DEFAULT as column_default, COLUMN_KEY as column_key, EXTRA as extra\n" .
                "FROM information_schema.COLUMNS\n" .
                "WHERE TABLE_SCHEMA = ?\n" .
                "ORDER BY TABLE_NAME, ORDINAL_POSITION",
                [$database]
            );
            foreach ($columnRows as $r) {
                $tableInfo[$r->table_name]['columns'][] = [
                    'name' => $r->column_name,
                    'type' => $r->column_type,
                    'nullable' => $r->is_nullable === 'YES',
                    'default' => $r->column_default,
                    'key' => $r->column_key,
                    'extra' => $r->extra,
                ];
            }

            $rows = $connection->select(
                "SELECT TABLE_NAME as table_name, COLUMN_NAME as column_name, REFERENCED_TABLE_NAME as referenced_table_name, REFERENCED_COLUMN_NAME as referenced_column_name, CONSTRAINT_NAME as constraint_name\n" .
                "FROM information_schema.KEY_COLUMN_USAGE\n" .
                "WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL\n" .
                "ORDER BY TABLE_NAME, COLUMN_NAME",
                [$database]
            );

            foreach ($rows as $row) {
                $relations[] = [
                    'table' => $row->table_name,
                    'column' => $row->column_name,
                    'ref_table' => $row->referenced_table_name,
                    'ref_column' => $row->referenced_column_name,
                    'constraint' => $row->constraint_name,
                ];
            }

            $triggerRows = $connection->select(
                "SELECT TRIGGER_NAME as trigger_name, EVENT_MANIPULATION as event, EVENT_OBJECT_TABLE as table_name, ACTION_TIMING as timing\n" .
                "FROM information_schema.TRIGGERS\n" .
                "WHERE TRIGGER_SCHEMA = ?\n" .
                "ORDER BY EVENT_OBJECT_TABLE, TRIGGER_NAME",
                [$database]
            );
            foreach ($triggerRows as $t) {
                $triggers[] = [
                    'name' => $t->trigger_name,
                    'event' => $t->event,
                    'timing' => $t->timing,
                    'table' => $t->table_name,
                ];
            }

            $routineRows = $connection->select(
                "SELECT ROUTINE_NAME as routine_name, ROUTINE_TYPE as routine_type\n" .
                "FROM information_schema.ROUTINES\n" .
                "WHERE ROUTINE_SCHEMA = ?\n" .
                "ORDER BY ROUTINE_TYPE, ROUTINE_NAME",
                [$database]
            );
            foreach ($routineRows as $r) {
                $routines[] = [
                    'name' => $r->routine_name,
                    'type' => $r->routine_type,
                ];
            }
        } elseif ($driver === 'pgsql') {
            $columnRows = $connection->select(
                "SELECT table_name as table_name, column_name as column_name, data_type as data_type, is_nullable as is_nullable, column_default as column_default\n" .
                "FROM information_schema.columns\n" .
                "WHERE table_schema = 'public'\n" .
                "ORDER BY table_name, ordinal_position"
            );
            foreach ($columnRows as $r) {
                $tableInfo[$r->table_name]['columns'][] = [
                    'name' => $r->column_name,
                    'type' => $r->data_type,
                    'nullable' => $r->is_nullable === 'YES',
                    'default' => $r->column_default,
                    'key' => null,
                    'extra' => null,
                ];
            }

            // Get PRIMARY KEY constraints
            $pkRows = $connection->select(
                "SELECT\n" .
                "  tc.table_name as table_name,\n" .
                "  kcu.column_name as column_name\n" .
                "FROM information_schema.table_constraints tc\n" .
                "JOIN information_schema.key_column_usage kcu\n" .
                "  ON tc.constraint_name = kcu.constraint_name\n" .
                "  AND tc.table_schema = kcu.table_schema\n" .
                "WHERE tc.constraint_type = 'PRIMARY KEY' AND tc.table_schema = 'public'\n" .
                "ORDER BY tc.table_name, kcu.ordinal_position"
            );

            foreach ($pkRows as $pk) {
                foreach ($tableInfo[$pk->table_name]['columns'] as &$col) {
                    if ($col['name'] === $pk->column_name) {
                        $col['key'] = 'PRI';
                        break;
                    }
                }
            }

            // Get UNIQUE constraints
            $uniqueRows = $connection->select(
                "SELECT\n" .
                "  tc.table_name as table_name,\n" .
                "  kcu.column_name as column_name\n" .
                "FROM information_schema.table_constraints tc\n" .
                "JOIN information_schema.key_column_usage kcu\n" .
                "  ON tc.constraint_name = kcu.constraint_name\n" .
                "  AND tc.table_schema = kcu.table_schema\n" .
                "WHERE tc.constraint_type = 'UNIQUE' AND tc.table_schema = 'public'\n" .
                "ORDER BY tc.table_name, kcu.ordinal_position"
            );

            foreach ($uniqueRows as $unique) {
                foreach ($tableInfo[$unique->table_name]['columns'] as &$col) {
                    if ($col['name'] === $unique->column_name && $col['key'] !== 'PRI') {
                        $col['key'] = 'UNI';
                        break;
                    }
                }
            }

            $rows = $connection->select(
                "SELECT\n" .
                "  tc.table_name as table_name,\n" .
                "  kcu.column_name as column_name,\n" .
                "  ccu.table_name as referenced_table_name,\n" .
                "  ccu.column_name as referenced_column_name,\n" .
                "  tc.constraint_name as constraint_name\n" .
                "FROM information_schema.table_constraints tc\n" .
                "JOIN information_schema.key_column_usage kcu\n" .
                "  ON tc.constraint_name = kcu.constraint_name\n" .
                "  AND tc.table_schema = kcu.table_schema\n" .
                "JOIN information_schema.constraint_column_usage ccu\n" .
                "  ON ccu.constraint_name = tc.constraint_name\n" .
                "  AND ccu.table_schema = tc.table_schema\n" .
                "WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = 'public'\n" .
                "ORDER BY tc.table_name, kcu.column_name"
            );

            foreach ($rows as $row) {
                $relations[] = [
                    'table' => $row->table_name,
                    'column' => $row->column_name,
                    'ref_table' => $row->referenced_table_name,
                    'ref_column' => $row->referenced_column_name,
                    'constraint' => $row->constraint_name,
                ];
            }

            $triggerRows = $connection->select(
                "SELECT trigger_name as trigger_name, event_manipulation as event, event_object_table as table_name, action_timing as timing\n" .
                "FROM information_schema.triggers\n" .
                "WHERE trigger_schema = 'public'\n" .
                "ORDER BY event_object_table, trigger_name"
            );
            foreach ($triggerRows as $t) {
                $triggers[] = [
                    'name' => $t->trigger_name,
                    'event' => $t->event,
                    'timing' => $t->timing,
                    'table' => $t->table_name,
                ];
            }

            $routineRows = $connection->select(
                "SELECT routine_name as routine_name, routine_type as routine_type\n" .
                "FROM information_schema.routines\n" .
                "WHERE routine_schema = 'public'\n" .
                "ORDER BY routine_type, routine_name"
            );
            foreach ($routineRows as $r) {
                $routines[] = [
                    'name' => $r->routine_name,
                    'type' => $r->routine_type,
                ];
            }
        } elseif ($driver === 'sqlite') {
            foreach ($tables as $table) {
                $infoRows = $connection->select('PRAGMA table_info(' . $table . ')');
                foreach ($infoRows as $r) {
                    $tableInfo[$table]['columns'][] = [
                        'name' => $r->name,
                        'type' => $r->type,
                        'nullable' => ((int) $r->notnull) === 0,
                        'default' => $r->dflt_value,
                        'key' => ((int) $r->pk) === 1 ? 'PRI' : null,
                        'extra' => null,
                    ];
                }
            }

            // Get UNIQUE constraints for SQLite
            foreach ($tables as $table) {
                $indexListRows = $connection->select('PRAGMA index_list(' . $table . ')');
                foreach ($indexListRows as $index) {
                    if ($index->unique == 1) {
                        $indexInfoRows = $connection->select('PRAGMA index_info(' . $index->name . ')');
                        foreach ($indexInfoRows as $indexInfo) {
                            foreach ($tableInfo[$table]['columns'] as &$col) {
                                if ($col['name'] === $indexInfo->name && $col['key'] !== 'PRI') {
                                    $col['key'] = 'UNI';
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            foreach ($tables as $table) {
                $pragmaRows = $connection->select('PRAGMA foreign_key_list(' . $table . ')');
                foreach ($pragmaRows as $r) {
                    $relations[] = [
                        'table' => $table,
                        'column' => $r->from,
                        'ref_table' => $r->table,
                        'ref_column' => $r->to,
                        'constraint' => null,
                    ];
                }
            }

            $triggerRows = $connection->select("SELECT name, tbl_name FROM sqlite_master WHERE type='trigger' ORDER BY tbl_name, name");
            foreach ($triggerRows as $t) {
                $triggers[] = [
                    'name' => $t->name,
                    'event' => null,
                    'timing' => null,
                    'table' => $t->tbl_name,
                ];
            }
        }

        $tableDescriptions = [
            'users' => 'Пользователи платформы (студенты/преподаватели/администраторы), профили и данные авторизации.',
            'roles' => 'Роли пользователей (например: admin, instructor, student).',
            'courses' => 'Курсы: основная информация, цены, статус публикации, статистика.',
            'categories' => 'Категории курсов (возможна иерархия через parent_id).',
            'modules' => 'Модули курса (логические разделы внутри курса).',
            'lessons' => 'Уроки в модулях: контент, тип урока, длительность, порядок.',
            'enrollments' => 'Записи студентов на курсы и прогресс по курсу.',
            'lesson_progress' => 'Прогресс прохождения конкретного урока студентом.',
            'reviews' => 'Отзывы и рейтинги курсов от пользователей, модерация.',
            'orders' => 'Заказы на покупку курсов/оплату.',
            'payments' => 'Платежи (статусы, суммы, gateway, транзакции) по заказам/курсам.',
            'certificates' => 'Сертификаты о завершении курсов.',
            'notifications' => 'Уведомления пользователей (если используется стандартная таблица/механизм).',
            'personal_access_tokens' => 'Токены API (Laravel Sanctum), если используется.',
            'password_reset_tokens' => 'Токены сброса пароля.',
            'failed_jobs' => 'Ошибки очередей (Laravel queues).',
            'migrations' => 'История применённых миграций.',
        ];

        $byTable = collect($relations)
            ->groupBy('table')
            ->map(fn ($items) => $items->values()->all())
            ->all();

        $tableInfo = collect($tableInfo)
            ->map(function ($info, $table) use ($tableDescriptions) {
                $info['description'] = $tableDescriptions[$table] ?? 'Служебная/прикладная таблица проекта.';

                $columns = collect($info['columns'] ?? []);
                $info['has_id'] = $columns->contains(fn ($c) => ($c['name'] ?? null) === 'id');
                $info['pk'] = $columns->filter(fn ($c) => ($c['key'] ?? null) === 'PRI')->pluck('name')->values()->all();
                $info['unique'] = $columns->filter(fn ($c) => ($c['key'] ?? null) === 'UNI')->pluck('name')->values()->all();

                return $info;
            })
            ->all();

        return view('admin.db.schema', [
            'driver' => $driver,
            'tables' => $tables,
            'relations' => $relations,
            'byTable' => $byTable,
            'tableInfo' => $tableInfo,
            'triggers' => $triggers,
            'routines' => $routines,
        ]);
    }

    // Управление категориями
    public function categories()
    {
        $categories = Category::with('parent', 'children')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:120|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Category::create($request->all());

        return back()->with('success', 'Категория создана');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:120|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update($request->all());

        return back()->with('success', 'Категория обновлена');
    }

    public function deleteCategory(Category $category)
    {
        // Проверяем, есть ли подкатегории
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Нельзя удалить категорию с подкатегориями');
        }

        // Проверяем, есть ли курсы в категории
        if ($category->courses()->count() > 0) {
            return back()->with('error', 'Нельзя удалить категорию с курсами');
        }

        $category->delete();

        return back()->with('success', 'Категория удалена');
    }

    // Управление курсами
    public function courses()
    {
        $courses = Course::with(['instructor', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.courses', compact('courses'));
    }

    public function showCourse(Course $course)
    {
        $course->load(['instructor', 'category', 'modules.lessons', 'enrollments.user']);

        return view('admin.courses.show', compact('course'));
    }

    public function updateCourseStatus(Request $request, Course $course)
    {
        $request->validate([
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $course->update($request->only(['is_published', 'is_featured']));

        return back()->with('success', 'Статус курса обновлен');
    }

    public function deleteCourse(Course $course)
    {
        // Проверяем, есть ли записи на курс
        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'Нельзя удалить курс со студентами');
        }

        $course->delete();

        return back()->with('success', 'Курс удален');
    }

    // Управление заказами
    public function orders()
    {
        $orders = Order::with(['user', 'payments.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['user', 'payments.course', 'enrollments.course']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled,refunded',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Статус заказа обновлен');
    }

    // Управление платежами
    public function payments()
    {
        $payments = Payment::with(['order.user', 'course.instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments', compact('payments'));
    }

    public function showPayment(Payment $payment)
    {
        $payment->load(['order.user', 'course.instructor']);

        return view('admin.payments.show', compact('payment'));
    }

    // Модерация отзывов
    public function reviews()
    {
        $reviews = Review::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    public function approveReview(Review $review)
    {
        $review->approve();

        return back()->with('success', 'Отзыв одобрен');
    }

    public function hideReview(Review $review)
    {
        $review->hide();

        return back()->with('success', 'Отзыв скрыт');
    }

    // Аналитика и отчеты
    public function analytics()
    {
        $stats = [
            'users_growth' => User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            
            'courses_growth' => Course::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            
            'revenue_monthly' => Payment::selectRaw('DATE_TRUNC(\'month\', paid_at) as month, SUM(amount) as revenue')
                ->where('status', 'completed')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get(),
        ];

        return view('admin.analytics', compact('stats'));
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth();
        $endDate = $request->end_date ?? now();

        $sales = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with(['course.instructor', 'order.user'])
            ->orderBy('paid_at', 'desc')
            ->paginate(50);

        $totalRevenue = $sales->sum('amount');
        $totalCommission = $sales->sum('commission');
        $totalInstructorEarnings = $sales->sum('instructor_earnings');

        return view('admin.reports.sales', compact(
            'sales',
            'totalRevenue',
            'totalCommission',
            'totalInstructorEarnings',
            'startDate',
            'endDate'
        ));
    }

    // Системные настройки
    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:100',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        // Здесь должна быть логика сохранения настроек
        // Например, в таблице settings или .env файле

        return back()->with('success', 'Настройки обновлены');
    }
}
