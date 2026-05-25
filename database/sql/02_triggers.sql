-- ========================================
-- PostgreSQL Триггеры для платформы курсов
-- Выполнять в PgAdmin после создания views
-- ========================================

-- Функция для обновления рейтинга курса
CREATE OR REPLACE FUNCTION update_course_rating()
RETURNS TRIGGER AS $$
BEGIN
    -- Обновляем рейтинг курса при добавлении/изменении/удалении отзыва
    UPDATE courses 
    SET 
        rating = COALESCE((
            SELECT AVG(rating) 
            FROM reviews 
            WHERE course_id = COALESCE(NEW.course_id, OLD.course_id) 
            AND is_visible = true
        ), 0),
        reviews_count = (
            SELECT COUNT(*) 
            FROM reviews 
            WHERE course_id = COALESCE(NEW.course_id, OLD.course_id) 
            AND is_visible = true
        )
    WHERE id = COALESCE(NEW.course_id, OLD.course_id);
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления рейтинга при отзывах
CREATE TRIGGER trg_update_course_rating
    AFTER INSERT OR UPDATE OR DELETE ON reviews
    FOR EACH ROW
    EXECUTE FUNCTION update_course_rating();

-- Функция для обновления количества студентов курса
CREATE OR REPLACE FUNCTION update_course_students_count()
RETURNS TRIGGER AS $$
BEGIN
    -- Обновляем количество студентов при записи/удалении
    UPDATE courses 
    SET students_count = (
        SELECT COUNT(*) 
        FROM enrollments 
        WHERE course_id = COALESCE(NEW.course_id, OLD.course_id)
    )
    WHERE id = COALESCE(NEW.course_id, OLD.course_id);
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления количества студентов
CREATE TRIGGER trg_update_enrollment_count
    AFTER INSERT OR DELETE ON enrollments
    FOR EACH ROW
    EXECUTE FUNCTION update_course_students_count();

-- Функция для обновления количества уроков в курсе
CREATE OR REPLACE FUNCTION update_course_lessons_count()
RETURNS TRIGGER AS $$
BEGIN
    -- При добавлении/удалении/изменении урока обновляем счетчик уроков в курсе
    UPDATE courses 
    SET lessons_count = (
        SELECT COUNT(*) 
        FROM lessons l
        JOIN modules m ON l.module_id = m.id
        WHERE m.course_id = (
            SELECT course_id 
            FROM modules 
            WHERE id = COALESCE(NEW.module_id, OLD.module_id)
        )
        AND l.is_published = true
    )
    WHERE id = (
        SELECT course_id 
        FROM modules 
        WHERE id = COALESCE(NEW.module_id, OLD.module_id)
    );
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления количества уроков
CREATE TRIGGER trg_update_lessons_count
    AFTER INSERT OR UPDATE OR DELETE ON lessons
    FOR EACH ROW
    EXECUTE FUNCTION update_course_lessons_count();

-- Функция для обновления длительности курса
CREATE OR REPLACE FUNCTION update_course_duration()
RETURNS TRIGGER AS $$
BEGIN
    -- Обновляем общую длительность курса при изменении уроков
    UPDATE courses 
    SET duration_minutes = (
        SELECT COALESCE(SUM(duration_minutes), 0)
        FROM lessons l
        JOIN modules m ON l.module_id = m.id
        WHERE m.course_id = (
            SELECT course_id 
            FROM modules 
            WHERE id = COALESCE(NEW.module_id, OLD.module_id)
        )
        AND l.is_published = true
    )
    WHERE id = (
        SELECT course_id 
        FROM modules 
        WHERE id = COALESCE(NEW.module_id, OLD.module_id)
    );
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления длительности курса
CREATE TRIGGER trg_update_course_duration
    AFTER INSERT OR UPDATE OR DELETE ON lessons
    FOR EACH ROW
    EXECUTE FUNCTION update_course_duration();

-- Функция для обновления прогресса записи
CREATE OR REPLACE FUNCTION update_enrollment_progress()
RETURNS TRIGGER AS $$
BEGIN
    -- При завершении урока обновляем общий прогресс записи
    UPDATE enrollments 
    SET 
        progress_percentage = (
            SELECT CASE 
                WHEN total.total_lessons > 0 
                THEN (completed.completed_lessons::DECIMAL / total.total_lessons::DECIMAL) * 100
                ELSE 0
            END
            FROM (
                SELECT COUNT(*) as total_lessons
                FROM lessons l
                JOIN modules m ON l.module_id = m.id
                WHERE m.course_id = NEW.course_id
                AND l.is_published = true
            ) total,
            (
                SELECT COUNT(*) as completed_lessons
                FROM lesson_progress lp
                JOIN lessons l ON lp.lesson_id = l.id
                JOIN modules m ON l.module_id = m.id
                WHERE lp.user_id = NEW.user_id
                AND m.course_id = NEW.course_id
                AND lp.is_completed = true
            ) completed
        ),
        completed_at = CASE 
            WHEN (
                SELECT COUNT(*) 
                FROM lesson_progress lp
                JOIN lessons l ON lp.lesson_id = l.id
                JOIN modules m ON l.module_id = m.id
                WHERE lp.user_id = NEW.user_id
                AND m.course_id = NEW.course_id
                AND lp.is_completed = true
            ) = (
                SELECT COUNT(*) 
                FROM lessons l
                JOIN modules m ON l.module_id = m.id
                WHERE m.course_id = NEW.course_id
                AND l.is_published = true
            )
            THEN NOW()
            ELSE NULL
        END
    WHERE user_id = NEW.user_id AND course_id = NEW.course_id;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления прогресса записи
CREATE TRIGGER trg_update_enrollment_progress
    AFTER INSERT OR UPDATE ON lesson_progress
    FOR EACH ROW
    WHEN (NEW.is_completed = true)
    EXECUTE FUNCTION update_enrollment_progress();

-- Функция для автоматического создания сертификата
CREATE OR REPLACE FUNCTION auto_create_certificate()
RETURNS TRIGGER AS $$
DECLARE
    cert_number TEXT;
    cert_data JSONB;
BEGIN
    -- При завершении курса автоматически создаем сертификат
    IF NEW.completed_at IS NOT NULL AND OLD.completed_at IS NULL THEN
        -- Генерируем номер сертификата
        cert_number := 'CERT-' || EXTRACT(YEAR FROM NEW.completed_at) || '-' || 
                       UPPER(SUBSTRING(MD5(NEW.id::TEXT || NEW.user_id::TEXT || NEW.course_id::TEXT), 1, 8));
        
        -- Формируем данные сертификата
        cert_data := jsonb_build_object(
            'student_name', (SELECT first_name || ' ' || last_name FROM users WHERE id = NEW.user_id),
            'course_title', (SELECT title FROM courses WHERE id = NEW.course_id),
            'instructor_name', (SELECT first_name || ' ' || last_name FROM users WHERE id = (SELECT instructor_id FROM courses WHERE id = NEW.course_id)),
            'completion_date', TO_CHAR(NEW.completed_at, 'DD.MM.YYYY'),
            'total_hours', (SELECT FLOOR(duration_minutes / 60) || 'ч ' || (duration_minutes % 60) || 'мин' FROM courses WHERE id = NEW.course_id),
            'certificate_number', cert_number
        );
        
        -- Создаем сертификат
        INSERT INTO certificates (
            user_id, 
            course_id, 
            enrollment_id, 
            certificate_number, 
            certificate_data, 
            file_path, 
            issued_at
        ) VALUES (
            NEW.user_id,
            NEW.course_id,
            NEW.id,
            cert_number,
            cert_data,
            'certificates/' || cert_number || '.pdf',
            NEW.completed_at
        );
        
        -- Обновляем запись с URL сертификата
        UPDATE enrollments 
        SET certificate_url = 'certificates/' || cert_number || '.pdf'
        WHERE id = NEW.id;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Триггер для автоматического создания сертификата
CREATE TRIGGER trg_auto_create_certificate
    AFTER UPDATE ON enrollments
    FOR EACH ROW
    EXECUTE FUNCTION auto_create_certificate();

-- Функция для аудита изменений курсов
CREATE OR REPLACE FUNCTION audit_course_changes()
RETURNS TRIGGER AS $$
BEGIN
    -- Записываем изменения курсов в лог аудита
    IF TG_OP = 'UPDATE' THEN
        INSERT INTO course_audit_log (
            course_id,
            changed_by,
            action,
            old_values,
            new_values,
            changed_at
        ) VALUES (
            COALESCE(NEW.id, OLD.id),
            COALESCE(NEW.instructor_id, OLD.instructor_id),
            'UPDATE',
            row_to_json(OLD),
            row_to_json(NEW),
            NOW()
        );
    ELSIF TG_OP = 'DELETE' THEN
        INSERT INTO course_audit_log (
            course_id,
            changed_by,
            action,
            old_values,
            new_values,
            changed_at
        ) VALUES (
            OLD.id,
            OLD.instructor_id,
            'DELETE',
            row_to_json(OLD),
            NULL,
            NOW()
        );
    ELSIF TG_OP = 'INSERT' THEN
        INSERT INTO course_audit_log (
            course_id,
            changed_by,
            action,
            old_values,
            new_values,
            changed_at
        ) VALUES (
            NEW.id,
            NEW.instructor_id,
            'INSERT',
            NULL,
            row_to_json(NEW),
            NOW()
        );
    END IF;
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Создаем таблицу аудита если не существует
CREATE TABLE IF NOT EXISTS course_audit_log (
    id SERIAL PRIMARY KEY,
    course_id INTEGER NOT NULL,
    changed_by INTEGER NOT NULL,
    action VARCHAR(10) NOT NULL,
    old_values JSONB,
    new_values JSONB,
    changed_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Триггер для аудита курсов
CREATE TRIGGER trg_audit_course_changes
    AFTER INSERT OR UPDATE OR DELETE ON courses
    FOR EACH ROW
    EXECUTE FUNCTION audit_course_changes();

-- Функция для обновления статистики преподавателя
CREATE OR REPLACE FUNCTION update_instructor_stats()
RETURNS TRIGGER AS $$
BEGIN
    -- При изменении курса обновляем кэшированную статистику преподавателя
    -- Это может быть использовано для оптимизации запросов
    UPDATE users 
    SET 
        updated_at = NOW()
    WHERE id = COALESCE(NEW.instructor_id, OLD.instructor_id);
    
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления статистики преподавателя
CREATE TRIGGER trg_update_instructor_stats
    AFTER INSERT OR UPDATE OR DELETE ON courses
    FOR EACH ROW
    EXECUTE FUNCTION update_instructor_stats();

-- Функция для проверки бизнес-правил
CREATE OR REPLACE FUNCTION validate_business_rules()
RETURNS TRIGGER AS $$
BEGIN
    -- Проверка: цена не может быть отрицательной
    IF NEW.price < 0 THEN
        RAISE EXCEPTION 'Цена курса не может быть отрицательной';
    END IF;
    
    -- Проверка: скидочная цена не может быть выше обычной
    IF NEW.discount_price IS NOT NULL AND NEW.discount_price > NEW.price THEN
        RAISE EXCEPTION 'Скидочная цена не может быть выше обычной цены';
    END IF;
    
    -- Проверка: рейтинг должен быть в диапазоне 0-5
    IF NEW.rating < 0 OR NEW.rating > 5 THEN
        RAISE EXCEPTION 'Рейтинг должен быть в диапазоне от 0 до 5';
    END IF;
    
    -- Проверка: количество уроков не может быть отрицательным
    IF NEW.lessons_count < 0 THEN
        RAISE EXCEPTION 'Количество уроков не может быть отрицательным';
    END IF;
    
    -- Проверка: количество студентов не может быть отрицательным
    IF NEW.students_count < 0 THEN
        RAISE EXCEPTION 'Количество студентов не может быть отрицательным';
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Триггер для валидации бизнес-правил
CREATE TRIGGER trg_validate_business_rules
    BEFORE INSERT OR UPDATE ON courses
    FOR EACH ROW
    EXECUTE FUNCTION validate_business_rules();

-- Функция для обновления времени последнего доступа пользователя
CREATE OR REPLACE FUNCTION update_user_last_access()
RETURNS TRIGGER AS $$
BEGIN
    -- Обновляем время последнего доступа при прогрессе урока
    UPDATE users 
    SET last_login_at = NOW()
    WHERE id = NEW.user_id;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Триггер для обновления времени последнего доступа
CREATE TRIGGER trg_update_user_last_access
    AFTER INSERT OR UPDATE ON lesson_progress
    FOR EACH ROW
    EXECUTE FUNCTION update_user_last_access();
