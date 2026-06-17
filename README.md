<h1 align="center">CourseHub 🎓</h1>

<p align="center">
  <strong>Современная платформа для онлайн-обучения с поддержкой десктопных клиентов.</strong>
</p>

<p align="center">
  <img src=".github/images/image1.png" width="80%" alt="Главная страница" />
</p>

## ✨ Особенности
- 🚀 **Laravel Backend**: Мощный и быстрый бэкенд на PHP 8.1 / Laravel 10.
- 🗄 **PostgreSQL**: Надежное хранение данных.
- 💻 **Десктоп-клиент (Electron)**: Полноэкранный, быстрый и нативный опыт работы для Windows ПК.
- 🎨 **Красивый UI**: Современный, адаптивный дизайн с плавными анимациями.
- 👥 **Система ролей**: Разделение на Студентов, Преподавателей и Администраторов.

---

## 📸 Галерея интерфейсов

### Дашборд студента и Плеер курса
<p align="center">
  <img src=".github/images/image2.png" width="48%" alt="Дашборд студента" />
  <img src=".github/images/image3.png" width="48%" alt="Плеер курса" />
</p>

### Дашборд преподавателя и Аналитика
<p align="center">
  <img src=".github/images/image4.png" width="48%" alt="Дашборд преподавателя" />
  <img src=".github/images/image5.png" width="48%" alt="Аналитика доходов" />
</p>

### Десктопное приложение и Дополнительные экраны
<p align="center">
  <img src=".github/images/image6.png" width="48%" alt="Десктопное приложение" />
  <img src=".github/images/image7.png" width="48%" alt="Дополнительный экран" />
</p>

### Прочие интерфейсы платформы
<p align="center">
  <img src=".github/images/image8.png" width="48%" alt="Интерфейс 1" />
  <img src=".github/images/image9.png" width="48%" alt="Интерфейс 2" />
  <img src=".github/images/image10.png" width="48%" alt="Интерфейс 3" />
  <img src=".github/images/image11.png" width="48%" alt="Интерфейс 4" />
</p>

---

## 🚀 Установка (Для разработчиков)

Если вы получили проект и хотите его развернуть без долгой настройки:

1. **Склонируйте репозиторий**:
   ```bash
   git clone https://github.com/kirsll/CourseHub-web-app-.git
   cd CourseHub-web-app-
   ```
2. **Восстановите зависимости**: 
   Распакуйте содержимое архива `CourseHub_Dependencies.zip` прямо в корневую папку (это восстановит `node_modules`, `vendor` и `.env` файл).
3. **Запустите локальный сервер**:
   ```bash
   php artisan serve
   ```
4. **Десктоп-клиент**: 
   Установочный файл `.exe` для Windows находится в папке `desktop-client/dist/`.

## 🛠 Стек технологий
* **Backend**: Laravel, PHP
* **Frontend**: Blade Templates, Vanilla CSS/JS
* **Desktop**: Electron, Node.js
* **Database**: PostgreSQL
