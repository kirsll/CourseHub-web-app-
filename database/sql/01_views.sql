-- ========================================
-- PostgreSQL Views для платформы курсов
-- Выполнять в PgAdmin после миграций Laravel
-- ========================================

-- View: v_course_catalog
-- Каталог курсов с рейтингами, количеством студентов и ценами
CREATE OR REPLACE VIEW v_course_catalog AS
SELECT 
    c.id,
    c.title,
    c.slug,
    c.description,
    c.thumbnail,
    c.price,
    c.discount_price,
    c.level,
    c.language,
    c.duration_minutes,
    c.lessons_count,
    c.students_count,
    c.rating,
    c.reviews_count,
    c.is_published,
    c.is_featured,
    c.created_at,
    c.updated_at,
    
    -- Информация о преподавателе
    u.id as instructor_id,
    u.first_name || ' ' || u.last_name as instructor_name,
    u.avatar as instructor_avatar,
    
    -- Информация о категории
    cat.name as category_name,
    cat.slug as category_slug,
    
    -- Вычисляемые поля
    CASE 
        WHEN c.discount_price IS NOT NULL AND c.discount_price > 0 
        THEN c.discount_price 
        ELSE c.price 
    END as current_price,
    
    CASE 
        WHEN c.discount_price IS NOT NULL AND c.discount_price > 0 AND c.price > 0 
        THEN ROUND(((c.price - c.discount_price) / c.price) * 100)
        ELSE 0 
    END as discount_percentage,
    
    -- Форматированная длительность
    CASE 
        WHEN c.duration_minutes >= 60 
        THEN CONCAT(FLOOR(c.duration_minutes / 60), 'ч ', (c.duration_minutes % 60), 'мин')
        ELSE CONCAT(c.duration_minutes, 'мин')
    END as formatted_duration,
    
    -- Форматированная цена
    TO_CHAR(CASE 
        WHEN c.discount_price IS NOT NULL AND c.discount_price > 0 
        THEN c.discount_price 
        ELSE c.price 
    END, 'FM9,999,990.00') || ' ₽' as formatted_price,
    
    -- Уровень сложности на русском
    CASE c.level
        WHEN 'beginner' THEN 'Начальный'
        WHEN 'intermediate' THEN 'Средний'
        WHEN 'advanced' THEN 'Продвинутый'
        ELSE c.level
    END as level_label
    
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
WHERE c.is_published = true;

-- View: v_student_enrollments
-- Записи студентов с прогрессом и информацией о курсах
CREATE OR REPLACE VIEW v_student_enrollments AS
SELECT 
    e.id,
    e.user_id,
    e.course_id,
    e.enrolled_at,
    e.completed_at,
    e.progress_percentage,
    e.paid_amount,
    
    -- Информация о студенте
    u.first_name || ' ' || u.last_name as student_name,
    u.email as student_email,
    
    -- Информация о курсе
    c.title as course_title,
    c.slug as course_slug,
    c.thumbnail as course_thumbnail,
    c.price as course_price,
    c.duration_minutes as course_duration,
    c.lessons_count as total_lessons,
    
    -- Информация о преподавателе
    instructor.first_name || ' ' || instructor.last_name as instructor_name,
    
    -- Статус записи
    CASE 
        WHEN e.completed_at IS NOT NULL THEN 'Завершен'
        WHEN e.progress_percentage > 0 THEN 'В процессе'
        ELSE 'Не начат'
    END as status,
    
    -- Прогресс в числовом виде
    COALESCE(
        (SELECT COUNT(*) 
         FROM lesson_progress lp 
         JOIN lessons l ON lp.lesson_id = l.id 
         JOIN modules m ON l.module_id = m.id 
         WHERE lp.user_id = e.user_id 
         AND m.course_id = e.course_id 
         AND lp.is_completed = true), 0
    ) as completed_lessons,
    
    -- Оставшиеся уроки
    c.lessons_count - COALESCE(
        (SELECT COUNT(*) 
         FROM lesson_progress lp 
         JOIN lessons l ON lp.lesson_id = l.id 
         JOIN modules m ON l.module_id = m.id 
         WHERE lp.user_id = e.user_id 
         AND m.course_id = e.course_id 
         AND lp.is_completed = true), 0
    ) as remaining_lessons,
    
    -- Форматированный прогресс
    TO_CHAR(e.progress_percentage, 'FM990.0') || '%' as formatted_progress,
    
    -- Форматированная сумма
    TO_CHAR(e.paid_amount, 'FM9,999,990.00') || ' ₽' as formatted_paid_amount,
    
    -- Дней с момента записи
    EXTRACT(DAY FROM (CURRENT_DATE - e.enrolled_at)) as days_since_enrollment,
    
    -- Дней до завершения (если завершен)
    CASE 
        WHEN e.completed_at IS NOT NULL 
        THEN EXTRACT(DAY FROM (e.completed_at - e.enrolled_at))
        ELSE NULL
    END as days_to_complete
    
FROM enrollments e
LEFT JOIN users u ON e.user_id = u.id
LEFT JOIN courses c ON e.course_id = c.id
LEFT JOIN users instructor ON c.instructor_id = instructor.id;

-- View: v_instructor_stats
-- Статистика преподавателей по курсам и доходам
CREATE OR REPLACE VIEW v_instructor_stats AS
SELECT 
    u.id as instructor_id,
    u.first_name || ' ' || u.last_name as instructor_name,
    u.email,
    u.avatar,
    
    -- Количество курсов
    (SELECT COUNT(*) FROM courses WHERE instructor_id = u.id AND is_published = true) as published_courses_count,
    (SELECT COUNT(*) FROM courses WHERE instructor_id = u.id) as total_courses_count,
    
    -- Общее количество студентов
    COALESCE((
        SELECT SUM(students_count) 
        FROM courses 
        WHERE instructor_id = u.id AND is_published = true
    ), 0) as total_students,
    
    -- Общий доход
    COALESCE((
        SELECT SUM(instructor_earnings) 
        FROM payments p
        JOIN courses c ON p.course_id = c.id
        WHERE c.instructor_id = u.id AND p.status = 'completed'
    ), 0) as total_earnings,
    
    -- Доход за последний месяц
    COALESCE((
        SELECT SUM(instructor_earnings) 
        FROM payments p
        JOIN courses c ON p.course_id = c.id
        WHERE c.instructor_id = u.id 
        AND p.status = 'completed'
        AND p.paid_at >= CURRENT_DATE - INTERVAL '30 days'
    ), 0) as monthly_earnings,
    
    -- Средний рейтинг курсов
    COALESCE((
        SELECT AVG(rating) 
        FROM courses 
        WHERE instructor_id = u.id AND is_published = true AND rating > 0
    ), 0) as average_rating,
    
    -- Общее количество отзывов
    COALESCE((
        SELECT SUM(reviews_count) 
        FROM courses 
        WHERE instructor_id = u.id AND is_published = true
    ), 0) as total_reviews,
    
    -- Последний курс
    (
        SELECT title 
        FROM courses 
        WHERE instructor_id = u.id AND is_published = true
        ORDER BY created_at DESC 
        LIMIT 1
    ) as latest_course_title,
    
    -- Форматированные суммы
    TO_CHAR(COALESCE((
        SELECT SUM(instructor_earnings) 
        FROM payments p
        JOIN courses c ON p.course_id = c.id
        WHERE c.instructor_id = u.id AND p.status = 'completed'
    ), 0), 'FM9,999,990.00') || ' ₽' as formatted_total_earnings,
    
    TO_CHAR(COALESCE((
        SELECT SUM(instructor_earnings) 
        FROM payments p
        JOIN courses c ON p.course_id = c.id
        WHERE c.instructor_id = u.id 
        AND p.status = 'completed'
        AND p.paid_at >= CURRENT_DATE - INTERVAL '30 days'
    ), 0), 'FM9,999,990.00') || ' ₽' as formatted_monthly_earnings
    
FROM users u
WHERE u.id IN (SELECT DISTINCT instructor_id FROM courses);

-- View: v_course_revenue
-- Аналитика доходов по курсам
CREATE OR REPLACE VIEW v_course_revenue AS
SELECT 
    c.id as course_id,
    c.title as course_title,
    c.slug,
    c.price,
    c.discount_price,
    c.students_count,
    c.rating,
    c.created_at,
    
    -- Информация о преподавателе
    u.first_name || ' ' || u.last_name as instructor_name,
    
    -- Категория
    cat.name as category_name,
    
    -- Продажи
    COALESCE(sales.total_sales, 0) as total_sales,
    COALESCE(sales.revenue, 0) as gross_revenue,
    COALESCE(sales.commission, 0) as total_commission,
    COALESCE(sales.instructor_earnings, 0) as net_instructor_earnings,
    
    -- Продажи за последний месяц
    COALESCE(monthly.monthly_sales, 0) as monthly_sales,
    COALESCE(monthly.monthly_revenue, 0) as monthly_revenue,
    COALESCE(monthly.monthly_commission, 0) as monthly_commission,
    COALESCE(monthly.monthly_instructor_earnings, 0) as monthly_instructor_earnings,
    
    -- Средняя цена продажи
    CASE 
        WHEN sales.total_sales > 0 
        THEN sales.revenue / sales.total_sales
        ELSE 0
    END as average_sale_price,
    
    -- Конверсия (записи / просмотры - если есть данные)
    CASE 
        WHEN c.students_count > 0 AND sales.total_sales > 0
        THEN ROUND((sales.total_sales::DECIMAL / c.students_count::DECIMAL) * 100, 2)
        ELSE 0
    END as conversion_rate,
    
    -- Форматированные суммы
    TO_CHAR(COALESCE(sales.revenue, 0), 'FM9,999,990.00') || ' ₽' as formatted_gross_revenue,
    TO_CHAR(COALESCE(sales.instructor_earnings, 0), 'FM9,999,990.00') || ' ₽' as formatted_instructor_earnings,
    TO_CHAR(COALESCE(monthly.monthly_revenue, 0), 'FM9,999,990.00') || ' ₽' as formatted_monthly_revenue
    
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
LEFT JOIN (
    SELECT 
        course_id,
        COUNT(*) as total_sales,
        SUM(amount) as revenue,
        SUM(commission) as commission,
        SUM(instructor_earnings) as instructor_earnings
    FROM payments 
    WHERE status = 'completed'
    GROUP BY course_id
) sales ON c.id = sales.course_id
LEFT JOIN (
    SELECT 
        course_id,
        COUNT(*) as monthly_sales,
        SUM(amount) as monthly_revenue,
        SUM(commission) as monthly_commission,
        SUM(instructor_earnings) as monthly_instructor_earnings
    FROM payments 
    WHERE status = 'completed' 
    AND paid_at >= CURRENT_DATE - INTERVAL '30 days'
    GROUP BY course_id
) monthly ON c.id = monthly.course_id
WHERE c.is_published = true;

-- View: v_popular_courses
-- Популярные курсы по продажам и рейтингам
CREATE OR REPLACE VIEW v_popular_courses AS
SELECT 
    c.*,
    u.first_name || ' ' || u.last_name as instructor_name,
    cat.name as category_name,
    
    -- Популярность по продажам
    COALESCE(sales.total_sales, 0) as sales_count,
    COALESCE(sales.revenue, 0) as total_revenue,
    
    -- Популярность по студентам
    c.students_count as enrollment_count,
    
    -- Рейтинг
    c.rating,
    c.reviews_count,
    
    -- Комбинированный счет популярности
    (COALESCE(sales.total_sales, 0) * 0.4 + 
     c.students_count * 0.3 + 
     c.rating * 20 * 0.2 + 
     c.reviews_count * 0.1) as popularity_score
    
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
LEFT JOIN (
    SELECT 
        course_id,
        COUNT(*) as total_sales,
        SUM(amount) as revenue
    FROM payments 
    WHERE status = 'completed'
    GROUP BY course_id
) sales ON c.id = sales.course_id
WHERE c.is_published = true
ORDER BY popularity_score DESC;
