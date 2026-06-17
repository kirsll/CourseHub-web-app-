<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;

class ExportDemo extends Command
{
    protected $signature = 'export:demo';
    protected $description = 'Exports full demo HTML pages for GitHub Pages';

    public function handle()
    {
        $demoDir = base_path('docs/demo');
        if (!File::exists($demoDir)) {
            File::makeDirectory($demoDir, 0755, true);
        }

        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        $courseId = \App\Models\Course::first()->id ?? 1;

        $routes = [
            ['url' => '/', 'file' => 'guest.html', 'role' => null],
            ['url' => '/courses', 'file' => 'catalog.html', 'role' => null],
            ['url' => '/courses/'.$courseId, 'file' => 'course.html', 'role' => null],
            
            ['url' => '/student/dashboard', 'file' => 'student.html', 'role' => 'student'],
            ['url' => '/student/courses', 'file' => 'student_courses.html', 'role' => 'student'],
            ['url' => '/student/courses/'.$courseId.'/learn', 'file' => 'student_learn.html', 'role' => 'student'],
            ['url' => '/student/profile', 'file' => 'student_profile.html', 'role' => 'student'],
            
            ['url' => '/instructor/dashboard', 'file' => 'instructor.html', 'role' => 'instructor'],
            ['url' => '/instructor/courses', 'file' => 'instructor_courses.html', 'role' => 'instructor'],
            ['url' => '/instructor/profile', 'file' => 'instructor_profile.html', 'role' => 'instructor'],
            
            ['url' => '/admin/dashboard', 'file' => 'admin.html', 'role' => 'admin'],
            ['url' => '/admin/users', 'file' => 'admin_users.html', 'role' => 'admin'],
            ['url' => '/admin/courses', 'file' => 'admin_courses.html', 'role' => 'admin'],
        ];

        // Ensure users exist
        foreach (['student', 'instructor', 'admin'] as $roleName) {
            $user = User::whereHas('role', function($q) use ($roleName) {
                $q->where('name', $roleName);
            })->first();
            
            if (!$user && $roleName == 'student') {
                $role = \App\Models\Role::where('name', 'student')->first();
                $user = User::factory()->create(['role_id' => $role->id, 'email' => 'teststudent@example.com']);
            }
        }

        foreach ($routes as $route) {
            $role = $route['role'];
            if ($role) {
                $user = User::whereHas('role', function($q) use ($role) {
                    $q->where('name', $role);
                })->first();
                if ($user) {
                    auth()->login($user);
                }
            } else {
                auth()->logout();
            }

            $request = Request::create($route['url'], 'GET');
            $request->setLaravelSession(app('session')->driver());

            $response = $kernel->handle($request);
            $content = $response->getContent();
            
            // Normalize all localhost URLs to absolute paths
            $content = str_replace(['http://127.0.0.1:8000', 'http://localhost'], '', $content);
            $content = str_replace('href=""', 'href="/"', $content);

            // Fix Asset Paths to be relative
            $content = str_replace('href="/build/', 'href="build/', $content);
            $content = str_replace('src="/build/', 'src="build/', $content);
            $content = str_replace('href="/storage/', 'href="storage/', $content);
            $content = str_replace('src="/storage/', 'src="storage/', $content);
            
            // Fix any other leading slash assets
            $content = str_replace('href="/css/', 'href="css/', $content);
            $content = str_replace('src="/js/', 'src="js/', $content);
            $content = str_replace('src="/images/', 'src="images/', $content);

            // Dynamic route rewriting (ONLY FOR A TAGS)
            foreach ($routes as $r) {
                // E.g. replace href="/" with href="guest.html"
                $pattern = '/href="\/'.preg_quote(ltrim($r['url'], '/'), '/').'"/';
                $content = preg_replace($pattern, 'href="'.$r['file'].'"', $content);
            }
            
            // Fallback for any other unmapped internal links in A tags
            // We match <a ... href="/something" ... >
            $content = preg_replace('/<a\s+([^>]*?)href="\/(?!build|storage)[^"]*"([^>]*?)>/i', '<a $1href="javascript:alert(\'Эта страница недоступна в демо-режиме\')"$2>', $content);
            
            // Disable all form actions
            $content = preg_replace('/action="[^"]*"/', 'action="javascript:alert(\'Отправка форм отключена\')"', $content);
            
            $bar = "
            <div style='position: fixed; bottom: 0; left: 0; width: 100%; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); border-top: 1px solid #334155; padding: 15px 20px; z-index: 999999; display: flex; justify-content: center; align-items: center; gap: 20px; color: white; font-family: Inter, sans-serif; box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.5); flex-wrap: wrap;'>
                <span style='font-weight: 600; font-size: 14px; opacity: 0.9;'>Режим эмуляции:</span>
                <a href='guest.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === null ? "background: #4F46E5; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>👤 Гость</a>
                <a href='student.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'student' ? "background: #10B981; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>🎓 Студент</a>
                <a href='instructor.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'instructor' ? "background: #8B5CF6; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>👨‍🏫 Преподаватель</a>
                <a href='admin.html' style='padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; " . ($role === 'admin' ? "background: #EF4444; color: white;" : "background: #1E293B; color: #94A3B8; border: 1px solid #334155;") . "'>🛡️ Админ</a>
                <div style='margin-left: auto; display: flex; gap: 10px;'>
                    <button onclick='alert(\"В режиме эмуляции формы и некоторые страницы отключены. Используйте меню и навигацию.\")' style='background: transparent; border: none; color: #64748B; cursor: pointer; font-size: 12px; text-decoration: underline;'>Инфо</button>
                    <a href='../index.html' style='padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; background: transparent; color: #94A3B8; text-decoration: none; border: 1px solid #334155;'>Закрыть</a>
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
            File::put($demoDir . '/' . $route['file'], $content);
            $this->info("Saved {$route['file']}");
            $kernel->terminate($request, $response);
        }

        // Copy Assets
        $this->info('Copying assets...');
        if (File::exists(public_path('build'))) {
            File::copyDirectory(public_path('build'), $demoDir . '/build');
        }
        if (File::exists(public_path('storage'))) {
            File::copyDirectory(public_path('storage'), $demoDir . '/storage');
        }

        $this->info('Export complete!');
    }
}
