<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OneSignalService;

class TestIOSNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ios-notifications {--type=all : Type of notification to test (all, location, event, news, investment)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test iOS push notifications using OneSignal';

    /**
     * OneSignal service instance
     *
     * @var OneSignalService
     */
    protected $oneSignalService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->oneSignalService = new OneSignalService();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧪 اختبار النوتيفيكيشن للـ iOS');
        $this->info('================================');
        $this->newLine();

        $type = $this->option('type');
        $results = [];

        switch ($type) {
            case 'location':
                $results['location'] = $this->testLocationNotification();
                break;
            case 'event':
                $results['event'] = $this->testEventNotification();
                break;
            case 'news':
                $results['news'] = $this->testNewsNotification();
                break;
            case 'investment':
                $results['investment'] = $this->testInvestmentNotification();
                break;
            case 'all':
            default:
                $results['general'] = $this->testGeneralNotification();
                $results['location'] = $this->testLocationNotification();
                $results['event'] = $this->testEventNotification();
                $results['news'] = $this->testNewsNotification();
                $results['investment'] = $this->testInvestmentNotification();
                break;
        }

        $this->displayResults($results);

        return Command::SUCCESS;
    }

    /**
     * Test general notification
     */
    protected function testGeneralNotification(): bool
    {
        $this->info('📱 اختبار النوتيفيكيشن العامة...');
        
        $result = $this->oneSignalService->sendNotification(
            'مرحباً من iOS! 🍎',
            'هذه رسالة اختبار للتأكد من عمل النوتيفيكيشن على أجهزة iOS',
            [
                'type' => 'test',
                'platform' => 'ios',
                'test_id' => 1,
                'app_url' => 'tourism_app://test/1'
            ]
        );

        $this->displayTestResult('النوتيفيكيشن العامة', $result);
        return $result;
    }

    /**
     * Test location notification
     */
    protected function testLocationNotification(): bool
    {
        $this->info('📍 اختبار نوتيفيكيشن الموقع...');
        
        $result = $this->oneSignalService->sendNotification(
            'موقع جديد تم إضافته! 📍',
            'تم إضافة موقع سياحي جديد: قلعة دمشق التاريخية',
            [
                'type' => 'location',
                'location_id' => 999,
                'location_name' => 'قلعة دمشق التاريخية',
                'action' => 'view_location',
                'app_url' => 'tourism_app://location/999'
            ]
        );

        $this->displayTestResult('نوتيفيكيشن الموقع', $result);
        return $result;
    }

    /**
     * Test event notification
     */
    protected function testEventNotification(): bool
    {
        $this->info('🎉 اختبار نوتيفيكيشن الحدث...');
        
        $result = $this->oneSignalService->sendNotification(
            'حدث جديد! 🎉',
            'تم إضافة حدث جديد: مهرجان دمشق للسياحة 2024',
            [
                'type' => 'event',
                'event_id' => 888,
                'event_title' => 'مهرجان دمشق للسياحة 2024',
                'action' => 'view_event',
                'app_url' => 'tourism_app://event/888'
            ]
        );

        $this->displayTestResult('نوتيفيكيشن الحدث', $result);
        return $result;
    }

    /**
     * Test news notification
     */
    protected function testNewsNotification(): bool
    {
        $this->info('📰 اختبار نوتيفيكيشن الخبر...');
        
        $result = $this->oneSignalService->sendNotification(
            'خبر جديد! 📰',
            'تم نشر خبر جديد: افتتاح متحف جديد في دمشق',
            [
                'type' => 'news',
                'news_id' => 777,
                'news_title' => 'افتتاح متحف جديد في دمشق',
                'action' => 'view_news',
                'app_url' => 'tourism_app://news/777'
            ]
        );

        $this->displayTestResult('نوتيفيكيشن الخبر', $result);
        return $result;
    }

    /**
     * Test investment notification
     */
    protected function testInvestmentNotification(): bool
    {
        $this->info('💰 اختبار نوتيفيكيشن الاستثمار...');
        
        $result = $this->oneSignalService->sendNotification(
            'استثمار جديد! 💰',
            'تم إضافة فرصة استثمارية جديدة: مشروع فندق سياحي',
            [
                'type' => 'investment',
                'investment_id' => 666,
                'investment_title' => 'مشروع فندق سياحي',
                'action' => 'view_investment',
                'app_url' => 'tourism_app://investment/666'
            ]
        );

        $this->displayTestResult('نوتيفيكيشن الاستثمار', $result);
        return $result;
    }

    /**
     * Display test result
     */
    protected function displayTestResult(string $testName, bool $result): void
    {
        if ($result) {
            $this->info("✅ $testName: نجح");
        } else {
            $this->error("❌ $testName: فشل");
        }
        $this->newLine();
    }

    /**
     * Display final results summary
     */
    protected function displayResults(array $results): void
    {
        $this->info('📊 ملخص النتائج:');
        $this->info('================');
        
        $successCount = 0;
        $totalCount = count($results);
        
        foreach ($results as $testType => $result) {
            $status = $result ? '✅' : '❌';
            $this->line("$testType: $status");
            if ($result) $successCount++;
        }

        $this->newLine();
        $this->info("نجح $successCount من أصل $totalCount اختبارات");

        if ($successCount === $totalCount) {
            $this->info('🎉 جميع الاختبارات نجحت! النوتيفيكيشن تعمل بشكل صحيح على iOS');
        } else {
            $this->warn('⚠️  بعض الاختبارات فشلت. تحقق من:');
            $this->line('   - إعدادات OneSignal');
            $this->line('   - صحة App ID');
            $this->line('   - صحة REST API Key');
            $this->line('   - إعدادات iOS Certificate');
            $this->line('   - اتصال الإنترنت');
        }

        $this->newLine();
        $this->info('📝 ملاحظات مهمة:');
        $this->info('================');
        $this->line('1. تأكد من أن التطبيق مثبت على جهاز iOS حقيقي');
        $this->line('2. تأكد من أن المستخدم وافق على النوتيفيكيشن');
        $this->line('3. تحقق من OneSignal Dashboard لرؤية إحصائيات الإرسال');
        $this->line('4. اختبر Deep Links بالضغط على النوتيفيكيشن');
        $this->line('5. تأكد من أن Certificate لم ينته (ينتهي في 7 أكتوبر 2026)');
    }
}
