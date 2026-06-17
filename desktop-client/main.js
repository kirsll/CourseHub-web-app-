const { app, BrowserWindow, Menu } = require('electron')

// URL вашего Laravel приложения (пока запускается локально через php artisan serve)
// Когда зальете на хостинг, просто поменяйте этот адрес на ваш домен (например, 'https://coursehub.com')
const APP_URL = 'http://127.0.0.1:8000';

function createWindow () {
  const mainWindow = new BrowserWindow({
    width: 1280,
    height: 800,
    // Убираем верхнее меню по умолчанию (File, Edit и т.д.)
    autoHideMenuBar: true, 
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      partition: 'persist:coursehub' // Уникальная сессия
    }
  })

  // Полностью отключаем системное меню
  Menu.setApplicationMenu(null);

  // Очистка кэша чтобы не показывалась старая пустая версия сайта
  mainWindow.webContents.session.clearCache().then(() => {
    // Разворачиваем на весь экран
    mainWindow.maximize();
    
    // Загружаем удаленный/локальный URL вместо локального HTML файла
    mainWindow.loadURL(APP_URL)
  });
}

app.whenReady().then(() => {
  createWindow()

  app.on('activate', function () {
    if (BrowserWindow.getAllWindows().length === 0) createWindow()
  })
})

app.on('window-all-closed', function () {
  if (process.platform !== 'darwin') app.quit()
})
