<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;

class ExportDemo extends Command
{
    protected $signature = 'export:demo';
    protected $description = 'Exports demo HTML pages for GitHub Pages';

    public function handle()
    {
        $demoDir = base_path('docs/demo');
        if (!File::exists($demoDir)) {
            File::makeDirectory($demoDir, 0755, true);
        }

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        $savePage = function($url, $filename, $role = null) use ($kernel, $demoDir) {
            if ($role) {
                $user = User::whereHas('role', function($q) use ($role) {
                    $q->where('name', $role);
                })->first();
                if ($user) {
                    auth()->login($user);
                } else {
                    $this->error("No user found for role: $role");
                    return;
                }
            } else {
                auth()->logout();
            }

            $request = Request::create($url, 'GET');
            
            // To process session correctly in a console command:
            $request->setLaravelSession(app('session')->driver());

            $response = $kernel->handle($request);
            $content = $response->getContent();
            
            // Replace links
            $content = str_replace('href="/courses"', 'href="catalog.html"', $content);
            $content = str_replace('href="/login"', 'href="student.html"', $content);
            $content = str_replace('href="/"', 'href="guest.html"', $content);
            $content = str_replace('href="/register"', 'href="guest.html"', $content);
            
            $bar = "
            <div style='position: fixed; bottom: 0; left: 0; width: 100%; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border-top: 1px solid #334155; padding: 15px 20px; z-index: 999999; display: flex; justify-content: center; align-items: center; gap: 20px; color: white; font-family: Inter, sans-serif; box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.5); flex-wrap: wrap;'>
                <span style='font-weight: 600; font-size: 14px; opacity: 0.9;'>Режим эмуляции:</span>
                <a href='guest.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === null ? "background: #4F46E5; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>👤 Гость</a>
                <a href='student.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'student' ? "background: #10B981; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>🎓 Студент</a>
                <a href='instructor.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'instructor' ? "background: #8B5CF6; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>👨‍🏫 Преподаватель</a>
                <a href='admin.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'admin' ? "background: #EF4444; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>🛡️ Админ</a>
                <div style='margin-left: auto; display: flex; gap: 10px;'>
                    <button onclick='alert(\"В режиме эмуляции формы и функционал отключены. Используйте верхнее меню и переключатель ролей для навигации по интерфейсу.\")' style='background: transparent; border: none; color: #64748B; cursor: pointer; font-size: 12px; text-decoration: underline;'>Инфо</button>
                    <a href='../index.html' style='padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; background: transparent; color: #94A3B8; text-decoration: none; border: 1px solid #334155;'>Закрыть эмуляцию</a>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('form').forEach(f => {
                        f.onsubmit = (e) => {
                            e.preventDefault();
                            alert('В режиме демонстрации отправка форм отключена.');
                        }
                    });
                });
            </script>
            </body>";
            
            $content = str_replace('</body>', $bar, $content);
            File::put($demoDir . '/' . $filename, $content);
            $this->info("Saved $filename");
            $kernel->terminate($request, $response);
        };

        $savePage('/', 'guest.html', null);
        $savePage('/student/dashboard', 'student.html', 'student');
        $savePage('/instructor/dashboard', 'instructor.html', 'instructor');
        $savePage('/admin/users', 'admin.html', 'admin');
        $savePage('/courses', 'catalog.html', null);

        $this->info('Export complete!');
    }
}
