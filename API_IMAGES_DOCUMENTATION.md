# توثيق API الصور للمواقع

## نظرة عامة

تم تحديث API المواقع ليدعم الصور بشكل كامل مع URLs مطلقة. جميع endpoints تعيد الآن URLs كاملة للصور.

## Endpoints المتاحة

### 1. قائمة المواقع مع الصور
```
GET /api/locations
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم جلب المواقع بنجاح",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Location Name",
        "name_ar": "اسم الموقع",
        "main_image": "locations/main/image.jpg",
        "main_image_url": "http://localhost/storage/locations/main/image.jpg",
        "active_images": [
          {
            "id": 1,
            "image_path": "locations/gallery/image1.jpg",
            "image_url": "http://localhost/storage/locations/gallery/image1.jpg",
            "caption": "Image description",
            "caption_ar": "وصف الصورة",
            "order": 1,
            "is_active": true
          }
        ]
      }
    ]
  }
}
```

### 2. تفاصيل موقع مع الصور
```
GET /api/locations/{id}
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم جلب الموقع بنجاح",
  "data": {
    "location": {
      "id": 1,
      "name": "Location Name",
      "name_ar": "اسم الموقع",
      "main_image": "locations/main/image.jpg",
      "main_image_url": "http://localhost/storage/locations/main/image.jpg",
      "active_images": [
        {
          "id": 1,
          "image_path": "locations/gallery/image1.jpg",
          "image_url": "http://localhost/storage/locations/gallery/image1.jpg",
          "caption": "Image description",
          "caption_ar": "وصف الصورة",
          "order": 1,
          "is_active": true
        }
      ]
    },
    "is_favorited": false
  }
}
```

### 3. صور موقع محدد (جديد)
```
GET /api/locations/{id}/images
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم جلب صور الموقع بنجاح",
  "data": {
    "main_image": {
      "path": "locations/main/image.jpg",
      "url": "http://localhost/storage/locations/main/image.jpg"
    },
    "gallery_images": [
      {
        "id": 1,
        "image_path": "locations/gallery/image1.jpg",
        "image_url": "http://localhost/storage/locations/gallery/image1.jpg",
        "caption": "Image description",
        "caption_ar": "وصف الصورة",
        "order": 1,
        "created_at": "2024-01-01T00:00:00.000000Z"
      }
    ]
  }
}
```

### 4. المواقع القريبة مع الصور
```
GET /api/locations/nearby?latitude=33.5138&longitude=36.2765&radius=10
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم جلب المواقع القريبة بنجاح",
  "data": [
    {
      "id": 1,
      "name": "Nearby Location",
      "name_ar": "موقع قريب",
      "main_image_url": "http://localhost/storage/locations/main/image.jpg",
      "active_images": [
        {
          "id": 1,
          "image_url": "http://localhost/storage/locations/gallery/image1.jpg",
          "caption_ar": "وصف الصورة"
        }
      ],
      "distance": 2.5
    }
  ]
}
```

### 5. المواقع المفضلة مع الصور
```
GET /api/locations/user/favorites
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم جلب المواقع المفضلة بنجاح",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Favorite Location",
        "name_ar": "موقع مفضل",
        "main_image_url": "http://localhost/storage/locations/main/image.jpg",
        "active_images": [
          {
            "id": 1,
            "image_url": "http://localhost/storage/locations/gallery/image1.jpg",
            "caption_ar": "وصف الصورة"
          }
        ]
      }
    ]
  }
}
```

### 6. البحث في المواقع مع الصور
```
GET /api/locations/search?q=اسم الموقع
```

**الاستجابة:**
```json
{
  "success": true,
  "message": "تم البحث في المواقع بنجاح",
  "data": [
    {
      "id": 1,
      "name": "Searched Location",
      "name_ar": "موقع البحث",
      "main_image_url": "http://localhost/storage/locations/main/image.jpg",
      "active_images": [
        {
          "id": 1,
          "image_url": "http://localhost/storage/locations/gallery/image1.jpg",
          "caption_ar": "وصف الصورة"
        }
      ]
    }
  ]
}
```

## معالجة الصور

### تنسيق البيانات
- **main_image**: مسار الصورة في التخزين
- **main_image_url**: URL كامل ومطلق للصورة الرئيسية
- **image_path**: مسار الصورة في التخزين
- **image_url**: URL كامل ومطلق لصورة المعرض

### أنواع الصور
1. **الصورة الرئيسية**: صورة واحدة لكل موقع
2. **صور المعرض**: عدة صور إضافية مع وصف

### ترتيب الصور
- الصور يتم ترتيبها حسب حقل `order`
- الصور النشطة فقط يتم إرجاعها (`is_active = true`)

## أمثلة استخدام

### JavaScript/Fetch
```javascript
// الحصول على صور موقع محدد
fetch('/api/locations/1/images', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  console.log('الصورة الرئيسية:', data.data.main_image.url);
  console.log('صور المعرض:', data.data.gallery_images);
});

// عرض الصور في واجهة المستخدم
function displayLocationImages(location) {
  if (location.main_image_url) {
    document.getElementById('main-image').src = location.main_image_url;
  }
  
  location.active_images.forEach(image => {
    const img = document.createElement('img');
    img.src = image.image_url;
    img.alt = image.caption_ar;
    document.getElementById('gallery').appendChild(img);
  });
}
```

### React Native
```javascript
// عرض الصور في React Native
const LocationImages = ({ location }) => {
  return (
    <View>
      {location.main_image_url && (
        <Image 
          source={{ uri: location.main_image_url }}
          style={{ width: 300, height: 200 }}
        />
      )}
      
      <ScrollView horizontal>
        {location.active_images.map(image => (
          <Image 
            key={image.id}
            source={{ uri: image.image_url }}
            style={{ width: 100, height: 100, margin: 5 }}
          />
        ))}
      </ScrollView>
    </View>
  );
};
```

### Flutter
```dart
// عرض الصور في Flutter
Widget buildLocationImages(Location location) {
  return Column(
    children: [
      if (location.mainImageUrl != null)
        Image.network(
          location.mainImageUrl,
          width: double.infinity,
          height: 200,
          fit: BoxFit.cover,
        ),
      
      SizedBox(height: 10),
      
      SizedBox(
        height: 100,
        child: ListView.builder(
          scrollDirection: Axis.horizontal,
          itemCount: location.activeImages.length,
          itemBuilder: (context, index) {
            final image = location.activeImages[index];
            return Padding(
              padding: EdgeInsets.only(right: 8),
              child: Image.network(
                image.imageUrl,
                width: 100,
                height: 100,
                fit: BoxFit.cover,
              ),
            );
          },
        ),
      ),
    ],
  );
}
```

## معالجة الأخطاء

### صورة غير موجودة
```json
{
  "main_image": null,
  "main_image_url": null
}
```

### موقع بدون صور
```json
{
  "main_image": null,
  "main_image_url": null,
  "active_images": []
}
```

## ملاحظات مهمة

1. **URLs كاملة**: جميع URLs تكون مطلقة وتبدأ بـ `http://` أو `https://`
2. **التوافق**: تعمل مع جميع المنصات (ويب، موبايل، تطبيقات)
3. **الأداء**: الصور يتم تحميلها عند الطلب فقط
4. **الأمان**: يتم التحقق من وجود الصور قبل إنشاء URLs
5. **التخزين**: الصور محفوظة في `storage/app/public/locations/`

## التحديثات المستقبلية

- دعم الصور المتعددة الأحجام (thumbnails, medium, large)
- ضغط الصور تلقائياً
- دعم CDN
- cache للـ URLs
- تحميل الصور بشكل تدريجي (lazy loading)
