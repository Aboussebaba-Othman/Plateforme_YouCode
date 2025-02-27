<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }
    
    public function index()
    {
        // Group settings by category
        $generalSettings = Setting::where('category', 'general')->get()->keyBy('key');
        $quizSettings = Setting::where('category', 'quiz')->get()->keyBy('key');
        $emailSettings = Setting::where('category', 'email')->get()->keyBy('key');
        $testSettings = Setting::where('category', 'test')->get()->keyBy('key');
        
        return view('admin.settings.index', compact(
            'generalSettings', 
            'quizSettings', 
            'emailSettings', 
            'testSettings'
        ));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable'
        ]);
        
        foreach ($request->settings as $key => $data) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                $setting->value = $data['value'];
                $setting->save();
            }
        }
        
        // Clear settings cache
        Cache::forget('app_settings');
        
        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully');
    }
    
    // File Upload Management
    public function fileSettings()
    {
        $fileSettings = Setting::where('category', 'file')->get()->keyBy('key');
        
        return view('admin.settings.file', compact('fileSettings'));
    }
    
    public function updateFileSettings(Request $request)
    {
        $request->validate([
            'max_file_size' => 'required|integer|min:1|max:50',
            'allowed_extensions' => 'required|string',
            'storage_driver' => 'required|in:local,s3'
        ]);
        
        Setting::updateOrCreate(
            ['key' => 'max_file_size', 'category' => 'file'],
            ['value' => $request->max_file_size]
        );
        
        Setting::updateOrCreate(
            ['key' => 'allowed_extensions', 'category' => 'file'],
            ['value' => $request->allowed_extensions]
        );
        
        Setting::updateOrCreate(
            ['key' => 'storage_driver', 'category' => 'file'],
            ['value' => $request->storage_driver]
        );
        
        if ($request->storage_driver === 's3') {
            $request->validate([
                's3_access_key' => 'required|string',
                's3_secret_key' => 'required|string',
                's3_region' => 'required|string',
                's3_bucket' => 'required|string'
            ]);
            
            Setting::updateOrCreate(
                ['key' => 's3_access_key', 'category' => 'file'],
                ['value' => $request->s3_access_key]
            );
            
            Setting::updateOrCreate(
                ['key' => 's3_secret_key', 'category' => 'file'],
                ['value' => $request->s3_secret_key]
            );
            
            Setting::updateOrCreate(
                ['key' => 's3_region', 'category' => 'file'],
                ['value' => $request->s3_region]
            );
            
            Setting::updateOrCreate(
                ['key' => 's3_bucket', 'category' => 'file'],
                ['value' => $request->s3_bucket]
            );
        }
        
        // Clear settings cache
        Cache::forget('app_settings');
        
        return redirect()->route('admin.settings.file')
            ->with('success', 'File settings updated successfully');
    }
    
    // System Maintenance
    public function maintenance()
    {
        $appEnv = app()->environment();
        $appDebug = config('app.debug') ? 'Enabled' : 'Disabled';
        $cacheDriver = config('cache.default');
        $queueDriver = config('queue.default');
        $laravelVersion = app()->version();
        
        return view('admin.settings.maintenance', compact(
            'appEnv',
            'appDebug',
            'cacheDriver',
            'queueDriver',
            'laravelVersion'
        ));
    }
    
    public function performMaintenance(Request $request)
    {
        $action = $request->input('action');
        $output = '';
        
        switch ($action) {
            case 'clear_cache':
                Artisan::call('cache:clear');
                $output = Artisan::output();
                break;
                
            case 'clear_config':
                Artisan::call('config:clear');
                $output = Artisan::output();
                break;
                
            case 'clear_views':
                Artisan::call('view:clear');
                $output = Artisan::output();
                break;
                
            case 'clear_routes':
                Artisan::call('route:clear');
                $output = Artisan::output();
                break;
                
            case 'clear_compiled':
                Artisan::call('clear-compiled');
                $output = Artisan::output();
                break;
                
            case 'optimize':
                Artisan::call('optimize');
                $output = Artisan::output();
                break;
                
            case 'optimize_clear':
                Artisan::call('optimize:clear');
                $output = Artisan::output();
                break;
                
            default:
                return redirect()->back()->with('error', 'Invalid maintenance action');
        }
        
        return redirect()->route('admin.settings.maintenance')
            ->with('success', 'Maintenance performed successfully')
            ->with('output', $output);
    }
    
    // Toggle maintenance mode
    public function toggleMaintenanceMode(Request $request)
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            $message = 'Application is now live';
        } else {
            $request->validate([
                'secret' => 'nullable|string',
                'message' => 'nullable|string'
            ]);
            
            $command = 'down';
            if ($request->filled('secret')) {
                $command .= ' --secret="' . $request->secret . '"';
            }
            if ($request->filled('message')) {
                $command .= ' --render="' . $request->message . '"';
            }
            
            Artisan::call($command);
            $message = 'Application is now in maintenance mode';
        }
        
        return redirect()->route('admin.settings.maintenance')
            ->with('success', $message);
    }
}