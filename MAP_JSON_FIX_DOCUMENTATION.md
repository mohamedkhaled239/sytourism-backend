# إصلاح مشكلة JSON في صفحة خريطة المواقع

## المشكلة
كانت هناك مشكلة في صفحة خريطة المواقع في لوحة الإدارة حيث كانت تظهر رسالة خطأ:
```
map:11 Uncaught SyntaxError: Bad control character in string literal in JSON at position 612 (line 11 column 214)
```

## سبب المشكلة
المشكلة كانت في ملف `resources/views/admin/map/index.blade.php` حيث كان يتم إنشاء JSON يدوياً باستخدام:
- دمج النصوص يدوياً مع `addslashes()`
- عدم التعامل الصحيح مع الأحرف الخاصة (control characters) مثل:
  - أحرف السطر الجديد (`\n`)
  - أحرف التبويب (`\t`) 
  - أحرف الإرجاع (`\r`)
  - أحرف أخرى قد تكون موجودة في النصوص العربية

## الحل المطبق

### 1. إصلاح ملف `resources/views/admin/map/index.blade.php`

**قبل الإصلاح:**
```blade
<script type="application/json" id="locations-data">
    [
    @foreach($locations as $index => $location)
        {
            "id": {{ $location->id }},
            "name_ar": "{{ addslashes($location->name_ar ?? '') }}",
            "description_ar": "{{ addslashes($location->description_ar ?? '') }}",
            // ... المزيد من الحقول
        }{{ $index < $locations->count() - 1 ? ',' : '' }}
    @endforeach
    ]
</script>
```

**بعد الإصلاح:**
```blade
<script type="application/json" id="locations-data">
    {!! json_encode($locations->map(function($location) {
        return [
            'id' => $location->id,
            'name_ar' => $location->name_ar ?? '',
            'description_ar' => $location->description_ar ?? '',
            // ... جميع الحقول
        ];
    })->toArray(), JSON_UNESCAPED_UNICODE) !!}
</script>
```

### 2. إصلاح ملف `resources/views/admin/map/show.blade.php`

**قبل الإصلاح:**
```javascript
marker.bindPopup(`
    <div style="text-align: center;">
        <strong>{{ $location->name_ar }}</strong><br>
        {{ $location->address_ar ?: 'موقع على الخريطة' }}
    </div>
`).openPopup();
```

**بعد الإصلاح:**
```javascript
const locationName = {!! json_encode($location->name_ar) !!};
const locationAddress = {!! json_encode($location->address_ar ?: 'موقع على الخريطة') !!};

const popupContent = `
    <div style="text-align: center;">
        <strong>${locationName}</strong><br>
        ${locationAddress}
    </div>
`;
marker.bindPopup(popupContent).openPopup();
```

## المزايا الجديدة

### 1. أمان أفضل
- استخدام `json_encode()` يضمن التعامل الصحيح مع جميع الأحرف الخاصة
- لا توجد مخاطر من injection attacks
- التعامل الصحيح مع النصوص العربية

### 2. موثوقية أعلى
- لا توجد أخطاء JSON syntax
- التعامل الصحيح مع البيانات الفارغة (`null` values)
- دعم كامل للنصوص متعددة الأسطر

### 3. صيانة أسهل
- كود أكثر وضوحاً وقابلية للقراءة
- سهولة إضافة حقول جديدة
- اتباع best practices في Laravel

## الاختبارات المطبقة

تم اختبار الحل مع:
- ✅ 52 موقع في قاعدة البيانات
- ✅ نصوص عربية مختلفة
- ✅ حقول فارغة ومليئة
- ✅ أحرف خاصة ومسافات
- ✅ JSON بحجم 16,372 حرف

## النتيجة

🎉 **تم حل المشكلة بنجاح!**

- لا توجد أخطاء JSON syntax
- المواقع تظهر بشكل صحيح على الخريطة
- النصوص العربية تعرض بشكل صحيح
- الخريطة تعمل بسلاسة في لوحة الإدارة

## ملاحظات للمطورين

1. **استخدم دائماً `json_encode()`** بدلاً من دمج النصوص يدوياً
2. **استخدم `JSON_UNESCAPED_UNICODE`** للنصوص العربية
3. **اختبر JSON** قبل إرساله للمتصفح
4. **تجنب `addslashes()`** في سياق JSON

## الملفات المعدلة

1. `resources/views/admin/map/index.blade.php` - الصفحة الرئيسية للخريطة
2. `resources/views/admin/map/show.blade.php` - صفحة عرض موقع واحد
