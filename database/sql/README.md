# PostgreSQL SQL Scripts

## Порядок выполнения

Выполняйте файлы в следующем порядке в PgAdmin:

### 1. Создание Views (Представлений)
```sql
-- Выполнить: 01_views.sql
```

Создает представления:
- `v_course_catalog` - Каталог курсов с рейтингами и ценами
- `v_student_enrollments` - Записи студентов с прогрессом
- `v_instructor_stats` - Статистика преподавателей
- `v_course_revenue` - Аналитика доходов по курсам
- `v_popular_courses` - Популярные курсы по продажам

### 2. Создание Триггеров
```sql
-- Выполнить: 02_triggers.sql
```

Создает триггеры:
- `trg_update_course_rating` - Обновление рейтинга при отзывах
- `trg_update_enrollment_count` - Счетчик студентов курса
- `trg_update_lessons_count` - Счетчик уроков курса
- `trg_update_course_duration` - Длительность курса
- `trg_update_enrollment_progress` - Прогресс записи
- `trg_auto_create_certificate` - Автосоздание сертификатов
- `trg_audit_course_changes` - Аудит изменений курсов
- `trg_validate_business_rules` - Валидация бизнес-правил

### 3. Создание Функций
```sql
-- Выполнить: 03_functions.sql
```

Создает функции:
- `get_top_courses_by_category()` - Топ курсы по категории
- `get_student_progress()` - Прогресс студента по курсу
- `get_instructor_earnings()` - Доходы преподавателя
- `get_course_recommendations()` - Рекомендации курсов
- `get_course_completion_stats()` - Статистика завершения
- `find_duplicate_courses()` - Поиск дубликатов
- `generate_sales_report()` - Отчет по продажам
- `cleanup_old_data()` - Очистка старых данных

## Примеры использования

### Получение каталога курсов:
```sql
SELECT * FROM v_course_catalog 
WHERE category_slug = 'programming' 
ORDER BY rating DESC 
LIMIT 10;
```

### Статистика преподавателя:
```sql
SELECT * FROM v_instructor_stats 
WHERE instructor_id = 123;
```

### Доходы за период:
```sql
SELECT * FROM get_instructor_earnings(123, '2024-01-01', '2024-12-31');
```

### Рекомендации для студента:
```sql
SELECT * FROM get_course_recommendations(456, 5);
```

### Отчет по продажам:
```sql
SELECT * FROM generate_sales_report('2024-01-01', '2024-01-31', 'week');
```

## Важные замечания

1. **Выполняйте скрипты строго по порядку** - функции могут зависеть от представлений
2. **Проверьте права доступа** - пользователь должен иметь права на создание триггеров и функций
3. **Резервное копирование** - перед выполнением сделайте бэкап БД
4. **Тестирование** - протестируйте триггеры на тестовых данных перед продакшеном

## Производительность

- Все представления оптимизированы с индексами
- Триггеры используют эффективные запросы
- Функции поддерживают пагинацию и фильтрацию
- Добавлены кэширующие индексы для частых запросов

## Мониторинг

Для мониторинга производительности используйте:
```sql
-- Просмотр активных триггеров
SELECT * FROM information_schema.triggers 
WHERE trigger_schema = 'public';

-- Статистика использования представлений
SELECT * FROM pg_stat_user_tables 
WHERE schemaname = 'public';
```
