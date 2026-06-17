<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$demoDir = __DIR__ . '/docs/demo';
if (!is_dir($demoDir)) {
    mkdir($demoDir, 0755, true);
}

function savePage($app, $kernel, $url, $filename, $role = null) {
    if ($role) {
        $user = \App\Models\User::whereHas('roles', function($q) use ($role) {
            $q->where('name', $role);
        })->first();
        if ($user) {
            auth()->login($user);
        } else {
            echo "No user found for role: $role\n";
            return;
        }
    } else {
        auth()->logout();
    }

    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    
    $content = $response->getContent();
    
    // Inject Demo Bar and replace Links
    $content = str_replace('href="/courses"', 'href="catalog.html"', $content);
    $content = str_replace('href="/login"', 'href="student.html"', $content);
    $content = str_replace('href="/"', 'href="guest.html"', $content);
    
    $bar = "
    <div style='position: fixed; bottom: 0; left: 0; width: 100%; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border-top: 1px solid #334155; padding: 15px 20px; z-index: 999999; display: flex; justify-content: center; align-items: center; gap: 20px; color: white; font-family: Inter, sans-serif; box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.5);'>
        <span style='font-weight: 600; font-size: 14px; opacity: 0.9;'>Режим эмуляции:</span>
        <a href='guest.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === null ? "background: #4F46E5; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>👤 Гость</a>
        <a href='student.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'student' ? "background: #10B981; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>🎓 Студент</a>
        <a href='instructor.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'instructor' ? "background: #8B5CF6; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>👨‍🏫 Преподаватель</a>
        <a href='admin.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'admin' ? "background: #EF4444; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>🛡️ Админ</a>
        <div style='margin-left: auto; display: flex; gap: 10px;'>
            <button onclick='alert(\"В режиме эмуляции формы и покупка отключены. Используйте переключатель ролей.\")' style='background: transparent; border: none; color: #64748B; cursor: pointer; font-size: 12px; text-decoration: underline;'>Инфо</button>
            <a href='../index.html' style='padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; background: transparent; color: #94A3B8; text-decoration: none; border: 1px solid #334155;'>Закрыть эмуляцию</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form').forEach(f => {
                f.onsubmit = (e) => {
                    e.preventDefault();
                    alert('Формы отключены в режиме демонстрации.');
                }
            });
        });
    </script>
    </body>";
    
    $content = str_replace('</body>', $bar, $content);
    
    file_put_contents(__DIR__ . '/docs/demo/' . $filename, $content);
    echo "Saved $filename\n";
    $kernel->terminate($request, $response);
}

savePage($app, $kernel, '/', 'guest.html', null);
savePage($app, $kernel, '/student/dashboard', 'student.html', 'student');
savePage($app, $kernel, '/instructor/dashboard', 'instructor.html', 'instructor');
savePage($app, $kernel, '/admin/users', 'admin.html', 'admin');
savePage($app, $kernel, '/courses', 'catalog.html', null);

echo "Export complete!\n";
