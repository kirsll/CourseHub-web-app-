-- ========================================
-- PostgreSQL Функции для платформы курсов
-- Дополнительные функции для бизнес-логики
-- ========================================

-- Функция для получения топ курсов по категории
CREATE OR REPLACE FUNCTION get_top_courses_by_category(
    p_category_id INTEGER,
    p_limit INTEGER DEFAULT 10
)
RETURNS TABLE (
    course_id INTEGER,
    title VARCHAR,
    instructor_name VARCHAR,
    rating DECIMAL,
    students_count INTEGER,
    price DECIMAL,
    formatted_price VARCHAR,
    popularity_score DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.id,
        c.title,
        u.first_name || ' ' || u.last_name,
        c.rating,
        c.students_count,
        c.price,
        TO_CHAR(c.price, 'FM9,999,990.00') || ' ₽',
        (COALESCE(sales.total_sales, 0) * 0.4 + 
         c.students_count * 0.3 + 
         c.rating * 20 * 0.2 + 
         c.reviews_count * 0.1) as popularity_score
    FROM courses c
    LEFT JOIN users u ON c.instructor_id = u.id
    LEFT JOIN course_category cc ON c.id = cc.course_id
    LEFT JOIN (
        SELECT course_id, COUNT(*) as total_sales
        FROM payments 
        WHERE status = 'completed'
        GROUP BY course_id
    ) sales ON c.id = sales.course_id
    WHERE cc.category_id = p_category_id 
    AND c.is_published = true
    ORDER BY popularity_score DESC
    LIMIT p_limit;
END;
$$ LANGUAGE plpgsql;

-- Функция для получения прогресса студента по курсу
CREATE OR REPLACE FUNCTION get_student_progress(
    p_user_id INTEGER,
    p_course_id INTEGER
)
RETURNS TABLE (
    total_lessons INTEGER,
    completed_lessons INTEGER,
    progress_percentage DECIMAL,
    time_spent_seconds INTEGER,
    estimated_completion_time INTERVAL,
    last_accessed TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        (SELECT COUNT(*) 
         FROM lessons l
         JOIN modules m ON l.module_id = m.id
         WHERE m.course_id = p_course_id
         AND l.is_published = true) as total_lessons,
        
        (SELECT COUNT(*) 
         FROM lesson_progress lp
         JOIN lessons l ON lp.lesson_id = l.id
         JOIN modules m ON l.module_id = m.id
         WHERE lp.user_id = p_user_id
         AND m.course_id = p_course_id
         AND lp.is_completed = true) as completed_lessons,
        
        COALESCE(e.progress_percentage, 0) as progress_percentage,
        COALESCE((SELECT SUM(watch_time_seconds)
                 FROM lesson_progress lp
                 JOIN lessons l ON lp.lesson_id = l.id
                 JOIN modules m ON l.module_id = m.id
                 WHERE lp.user_id = p_user_id
                 AND m.course_id = p_course_id), 0) as time_spent_seconds,
        
        CASE 
            WHEN e.enrolled_at IS NOT NULL AND e.progress_percentage > 0
            THEN (CURRENT_DATE - e.enrolled_at) * (100 / e.progress_percentage) - (CURRENT_DATE - e.enrolled_at)
            ELSE NULL
        END as estimated_completion_time,
        
        (SELECT MAX(last_accessed_at)
         FROM lesson_progress lp
         JOIN lessons l ON lp.lesson_id = l.id
         JOIN modules m ON l.module_id = m.id
         WHERE lp.user_id = p_user_id
         AND m.course_id = p_course_id) as last_accessed
    
    FROM enrollments e
    WHERE e.user_id = p_user_id AND e.course_id = p_course_id;
END;
$$ LANGUAGE plpgsql;

-- Функция для расчета дохода преподавателя за период
CREATE OR REPLACE FUNCTION get_instructor_earnings(
    p_instructor_id INTEGER,
    p_start_date DATE DEFAULT CURRENT_DATE - INTERVAL '30 days',
    p_end_date DATE DEFAULT CURRENT_DATE
)
RETURNS TABLE (
    course_id INTEGER,
    course_title VARCHAR,
    sales_count INTEGER,
    gross_revenue DECIMAL,
    commission DECIMAL,
    net_earnings DECIMAL,
    average_price DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c.id,
        c.title,
        COUNT(p.id) as sales_count,
        SUM(p.amount) as gross_revenue,
        SUM(p.commission) as commission,
        SUM(p.instructor_earnings) as net_earnings,
        CASE 
            WHEN COUNT(p.id) > 0 
            THEN SUM(p.amount) / COUNT(p.id)
            ELSE 0
        END as average_price
    FROM payments p
    JOIN courses c ON p.course_id = c.id
    WHERE c.instructor_id = p_instructor_id
    AND p.status = 'completed'
    AND DATE(p.paid_at) BETWEEN p_start_date AND p_end_date
    GROUP BY c.id, c.title
    ORDER BY net_earnings DESC;
END;
$$ LANGUAGE plpgsql;

-- Функция для получения рекомендаций курсов для студента
CREATE OR REPLACE FUNCTION get_course_recommendations(
    p_user_id INTEGER,
    p_limit INTEGER DEFAULT 5
)
RETURNS TABLE (
    course_id INTEGER,
    title VARCHAR,
    instructor_name VARCHAR,
    rating DECIMAL,
    students_count INTEGER,
    price DECIMAL,
    match_score DECIMAL,
    recommendation_reason VARCHAR
) AS $$
BEGIN
    RETURN QUERY
    WITH user_categories AS (
        SELECT DISTINCT cc.category_id
        FROM enrollments e
        JOIN course_category cc ON e.course_id = cc.course_id
        WHERE e.user_id = p_user_id
    ),
    user_levels AS (
        SELECT DISTINCT c.level
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.user_id = p_user_id
    )
    SELECT 
        c.id,
        c.title,
        u.first_name || ' ' || u.last_name as instructor_name,
        c.rating,
        c.students_count,
        c.price,
        -- Расчет релевантности
        (CASE 
            WHEN cc.category_id IN (SELECT category_id FROM user_categories) THEN 40
            ELSE 0
        END +
        CASE 
            WHEN c.level IN (SELECT level FROM user_levels) THEN 30
            ELSE 0
        END +
        LEAST(c.rating * 10, 20) +
        LEAST(c.students_count / 100, 10)) as match_score,
        
        -- Причина рекомендации
        CASE 
            WHEN cc.category_id IN (SELECT category_id FROM user_categories) 
            AND c.level IN (SELECT level FROM user_levels) 
            THEN 'Похожий курс в вашей любимой категории и уровне'
            WHEN cc.category_id IN (SELECT category_id FROM user_categories) 
            THEN 'Популярный курс в вашей любимой категории'
            WHEN c.level IN (SELECT level FROM user_levels) 
            THEN 'Курс вашего уровня сложности'
            ELSE 'Популярный курс с высоким рейтингом'
        END as recommendation_reason
    
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    JOIN course_category cc ON c.id = cc.course_id
    WHERE c.is_published = true
    AND c.id NOT IN (
        SELECT course_id FROM enrollments WHERE user_id = p_user_id
    )
    ORDER BY match_score DESC, c.rating DESC
    LIMIT p_limit;
END;
$$ LANGUAGE plpgsql;

-- Функция для получения статистики завершения курсов
CREATE OR REPLACE FUNCTION get_course_completion_stats(
    p_course_id INTEGER
)
RETURNS TABLE (
    total_enrollments INTEGER,
    completed_enrollments INTEGER,
    completion_rate DECIMAL,
    average_completion_days INTEGER,
    median_completion_days INTEGER,
    drop_off_points JSON
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        COUNT(*) as total_enrollments,
        COUNT(completed_at) as completed_enrollments,
        CASE 
            WHEN COUNT(*) > 0 
            THEN (COUNT(completed_at)::DECIMAL / COUNT(*)::DECIMAL) * 100
            ELSE 0
        END as completion_rate,
        
        -- Среднее время завершения
        EXTRACT(DAYS FROM AVG(completed_at - enrolled_at))::INTEGER as average_completion_days,
        
        -- Медиана времени завершения
        (
            SELECT EXTRACT(DAYS FROM (completed_at - enrolled_at))::INTEGER
            FROM enrollments 
            WHERE course_id = p_course_id 
            AND completed_at IS NOT NULL
            ORDER BY completed_at - enrolled_at
            OFFSET (SELECT COUNT(*) / 2 FROM enrollments WHERE course_id = p_course_id AND completed_at IS NOT NULL)
            LIMIT 1
        ) as median_completion_days,
        
        -- Точки оттока (где студенты прекращают обучение)
        (
            SELECT jsonb_agg(
                jsonb_build_object(
                    'module_title', m.title,
                    'drop_off_count', drop_offs.count
                )
            )
            FROM (
                SELECT 
                    m.id,
                    m.title,
                    COUNT(*) as drop_off_count
                FROM modules m
                LEFT JOIN lessons l ON m.id = l.module_id
                LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id
                WHERE m.course_id = p_course_id
                GROUP BY m.id, m.title
                ORDER BY drop_off_count DESC
                LIMIT 5
            ) drop_offs
            JOIN modules m ON drop_offs.id = m.id
        ) as drop_off_points
    
    FROM enrollments 
    WHERE course_id = p_course_id;
END;
$$ LANGUAGE plpgsql;

-- Функция для поиска дубликатов курсов
CREATE OR REPLACE FUNCTION find_duplicate_courses()
RETURNS TABLE (
    course_id INTEGER,
    title VARCHAR,
    instructor_id INTEGER,
    instructor_name VARCHAR,
    similarity_score DECIMAL,
    potential_duplicate_id INTEGER,
    potential_duplicate_title VARCHAR
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        c1.id as course_id,
        c1.title,
        c1.instructor_id,
        u1.first_name || ' ' || u1.last_name as instructor_name,
        
        -- Простой расчет схожести заголовков
        CASE 
            WHEN LOWER(c1.title) = LOWER(c2.title) THEN 100
            WHEN LOWER(c1.title) LIKE '%' || LOWER(c2.title) || '%' OR LOWER(c2.title) LIKE '%' || LOWER(c1.title) || '%' THEN 80
            WHEN similarity(c1.title, c2.title) > 0.7 THEN similarity(c1.title, c2.title) * 100
            ELSE 0
        END as similarity_score,
        
        c2.id as potential_duplicate_id,
        c2.title as potential_duplicate_title
    
    FROM courses c1
    JOIN courses c2 ON c1.id != c2.id
    JOIN users u1 ON c1.instructor_id = u1.id
    WHERE (
        LOWER(c1.title) = LOWER(c2.title) OR
        LOWER(c1.title) LIKE '%' || LOWER(c2.title) || '%' OR
        LOWER(c2.title) LIKE '%' || LOWER(c1.title) || '%' OR
        similarity(c1.title, c2.title) > 0.7
    )
    AND c1.instructor_id = c2.instructor_id
    AND similarity_score > 70
    ORDER BY similarity_score DESC;
END;
$$ LANGUAGE plpgsql;

-- Функция для генерации отчета по продажам
CREATE OR REPLACE FUNCTION generate_sales_report(
    p_start_date DATE DEFAULT CURRENT_DATE - INTERVAL '30 days',
    p_end_date DATE DEFAULT CURRENT_DATE,
    p_group_by VARCHAR DEFAULT 'day' -- day, week, month
)
RETURNS TABLE (
    period DATE,
    total_sales INTEGER,
    gross_revenue DECIMAL,
    commission DECIMAL,
    net_revenue DECIMAL,
    unique_customers INTEGER,
    average_order_value DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        CASE p_group_by
            WHEN 'day' THEN DATE(p.paid_at)
            WHEN 'week' THEN DATE_TRUNC('week', p.paid_at)::DATE
            WHEN 'month' THEN DATE_TRUNC('month', p.paid_at)::DATE
            ELSE DATE(p.paid_at)
        END as period,
        
        COUNT(*) as total_sales,
        SUM(p.amount) as gross_revenue,
        SUM(p.commission) as commission,
        SUM(p.amount - p.commission) as net_revenue,
        COUNT(DISTINCT p.order_id) as unique_customers,
        CASE 
            WHEN COUNT(*) > 0 
            THEN SUM(p.amount) / COUNT(*)
            ELSE 0
        END as average_order_value
    
    FROM payments p
    WHERE p.status = 'completed'
    AND DATE(p.paid_at) BETWEEN p_start_date AND p_end_date
    GROUP BY 
        CASE p_group_by
            WHEN 'day' THEN DATE(p.paid_at)
            WHEN 'week' THEN DATE_TRUNC('week', p.paid_at)::DATE
            WHEN 'month' THEN DATE_TRUNC('month', p.paid_at)::DATE
            ELSE DATE(p.paid_at)
        END
    ORDER BY period;
END;
$$ LANGUAGE plpgsql;

-- Функция для очистки старых данных
CREATE OR REPLACE FUNCTION cleanup_old_data(
    p_days_to_keep INTEGER DEFAULT 365
)
RETURNS TABLE (
    table_name VARCHAR,
    records_deleted INTEGER
) AS $$
DECLARE
    cleanup_date TIMESTAMP;
BEGIN
    cleanup_date := CURRENT_DATE - INTERVAL '1 day' * p_days_to_keep;
    
    -- Очистка старых неудачных попыток тестов
    RETURN QUERY
    SELECT 
        'quiz_attempts'::VARCHAR,
        COUNT(*)
    FROM quiz_attempts
    WHERE created_at < cleanup_date
    AND status = 'failed'
    AND is_passed = false;
    
    DELETE FROM quiz_attempts
    WHERE created_at < cleanup_date
    AND status = 'failed'
    AND is_passed = false;
    
    -- Очистка старых логов аудита
    RETURN QUERY
    SELECT 
        'course_audit_log'::VARCHAR,
        COUNT(*)
    FROM course_audit_log
    WHERE changed_at < cleanup_date;
    
    DELETE FROM course_audit_log
    WHERE changed_at < cleanup_date;
    
END;
$$ LANGUAGE plpgsql;
