document.addEventListener('DOMContentLoaded', function() {
    console.log('تم تحميل DOM');
    
    // التحقق من وجود عنصر الخريطة
    const mapElement = document.getElementById('map');
    console.log('عنصر الخريطة:', mapElement);
    
    if (!mapElement) {
        console.error('عنصر الخريطة غير موجود');
        return;
    }

    try {
        console.log('بدء تهيئة الخريطة');
        console.log('مكتبة Leaflet:', typeof L !== 'undefined' ? 'متوفرة' : 'غير متوفرة');
        
        // التحقق من تحميل مكتبة Leaflet
        if (typeof L === 'undefined') {
            console.error('مكتبة Leaflet غير متوفرة');
            return;
        }
        
        // إنشاء الخريطة
        console.log('إنشاء كائن الخريطة');
        const map = L.map('map', {
            center: [24.774265, 46.738586],
            zoom: 6,
            zoomControl: true,
            attributionControl: true
        }); // إحداثيات المملكة العربية السعودية

        // إضافة طبقة الخريطة
        console.log('إضافة طبقة الخريطة');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // إضافة مقياس للخريطة
        L.control.scale({ position: 'bottomright', imperial: false }).addTo(map);

        // إضافة المواقع إلى الخريطة إذا كانت متوفرة
        if (typeof locations !== 'undefined' && locations.length > 0) {
            // إنشاء مجموعة للعلامات مع تجميع العلامات المتقاربة
            const markers = L.featureGroup().addTo(map);
            
            // إضافة زر للتحكم في عرض جميع المواقع
            const showAllButton = L.control({position: 'topleft'});
            showAllButton.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                div.innerHTML = '<a href="#" title="عرض جميع المواقع" style="display: block; width: 30px; height: 30px; line-height: 30px; text-align: center; font-size: 18px; background-color: white;">🔍</a>';
                div.onclick = function() {
                    map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                    return false;
                };
                return div;
            };
            showAllButton.addTo(map);
            
            locations.forEach(function(location) {
                // التأكد من وجود إحداثيات صالحة
                if (location.latitude && location.longitude) {
                    // إنشاء أيقونة مخصصة للعلامة
                    let markerIcon = L.icon({
                        iconUrl: '/js/leaflet/images/marker-icon.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowUrl: '/js/leaflet/images/marker-shadow.png',
                        shadowSize: [41, 41]
                    });
                    
                    // إذا كان للموقع نوع سياحة، يمكن استخدام لون مختلف
                    if (location.tourism_type && location.tourism_type.color) {
                        // استخدام الأيقونة الافتراضية مع اللون المخصص
                        markerIcon = L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background-color: ${location.tourism_type.color}; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);"></div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12],
                            popupAnchor: [0, -12]
                        });
                    }
                    
                    const marker = L.marker([location.latitude, location.longitude], { icon: markerIcon })
                        .addTo(markers);
                    
                    // إنشاء محتوى النافذة المنبثقة
                    let popupContent = `
                        <div class="location-popup">
                            <h5>${location.name_ar}</h5>
                    `;
                    
                    // إضافة صورة الموقع إذا كانت متوفرة
                    if (location.image) {
                        popupContent += `
                            <div class="location-image mb-2">
                                <img src="/storage/${location.image}" alt="${location.name_ar}" class="img-fluid rounded">
                            </div>
                        `;
                    }
                    
                    // إضافة الفئات إذا كانت موجودة
                    if (location.categories && location.categories.length > 0) {
                        popupContent += '<div class="mb-2">';
                        location.categories.forEach(function(category) {
                            popupContent += `<span class="badge" style="background-color: ${category.color}">${category.name_ar}</span> `;
                        });
                        popupContent += '</div>';
                    }
                    
                    // إضافة المحافظة إذا كانت موجودة
                    if (location.governorate) {
                        popupContent += `<div><strong>المحافظة:</strong> ${location.governorate.name_ar}</div>`;
                    }
                    
                    // إضافة نوع السياحة إذا كان موجود
                    if (location.tourism_type) {
                        popupContent += `<div><strong>نوع السياحة:</strong> ${location.tourism_type.name_ar}</div>`;
                    }
                    
                    // إضافة العنوان إذا كان موجود
                    if (location.address_ar) {
                        popupContent += `<div><strong>العنوان:</strong> ${location.address_ar}</div>`;
                    }
                    
                    // إضافة أزرار للتفاعل مع الموقع
                    popupContent += `
                        <div class="popup-buttons">
                            <a href="/admin/map/${location.id}" class="btn btn-primary btn-sm">عرض التفاصيل</a>
                            <a href="https://www.google.com/maps/search/?api=1&query=${location.latitude},${location.longitude}" target="_blank" class="btn btn-success btn-sm">عرض على خريطة جوجل</a>
                        </div>
                    </div>
                    `;
                    
                    marker.bindPopup(popupContent);
                }
            });
            
            // ضبط حدود الخريطة لتشمل جميع العلامات
            if (markers.getLayers().length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            }
        } else {
            console.log('لا توجد مواقع لعرضها على الخريطة');
        }
    } catch (error) {
        console.error('حدث خطأ أثناء تهيئة الخريطة:', error);
    }
});