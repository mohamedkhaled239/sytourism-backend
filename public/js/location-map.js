document.addEventListener('DOMContentLoaded', function() {
    // التحقق من وجود عنصر الخريطة
    const mapElement = document.getElementById('location-map');
    if (!mapElement) {
        console.error('عنصر خريطة الموقع غير موجود');
        return;
    }

    try {
        // إنشاء الخريطة
        const locationMap = L.map('location-map');
        
        // إضافة طبقة الخريطة
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(locationMap);
        
        // الحصول على إحداثيات الموقع من عناصر مخفية
        const latitudeElement = document.getElementById('location-latitude');
        const longitudeElement = document.getElementById('location-longitude');
        const nameElement = document.getElementById('location-name');
        const addressElement = document.getElementById('location-address');
        
        if (latitudeElement && longitudeElement) {
            const latitude = parseFloat(latitudeElement.value);
            const longitude = parseFloat(longitudeElement.value);
            const name = nameElement ? nameElement.value : '';
            const address = addressElement ? addressElement.value : '';
            
            // التحقق من صحة الإحداثيات
            if (!isNaN(latitude) && !isNaN(longitude)) {
                // إنشاء أيقونة مخصصة للعلامة
                const markerIcon = L.icon({
                    iconUrl: '/js/leaflet/images/marker-icon.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: '/js/leaflet/images/marker-shadow.png',
                    shadowSize: [41, 41]
                });
                
                // إضافة علامة للموقع
                const marker = L.marker([latitude, longitude], { icon: markerIcon }).addTo(locationMap);
                
                // ضبط مركز الخريطة على الموقع
                locationMap.setView([latitude, longitude], 15);
                
                // إضافة نافذة منبثقة للعلامة مع أزرار لعرض الموقع على خريطة جوجل وعرض التفاصيل
                const popupContent = `
                    <div class="location-popup">
                        <strong>${name}</strong>
                        <p>${address}</p>
                        <div class="popup-buttons">
                            <a href="https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}" target="_blank" class="btn btn-success btn-sm">عرض على خريطة جوجل</a>
                            <a href="javascript:void(0)" onclick="window.location.reload()" class="btn btn-primary btn-sm">تحديث التفاصيل</a>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent).openPopup();
            } else {
                console.error('إحداثيات غير صالحة');
            }
        } else {
            console.error('عناصر الإحداثيات غير موجودة');
        }
    } catch (error) {
        console.error('حدث خطأ أثناء تهيئة خريطة الموقع:', error);
    }
});