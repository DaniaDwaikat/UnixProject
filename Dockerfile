FROM php:8.2-apache
# تثبيت تعريفات قاعدة البيانات كما في شرح الحزم
RUN docker-php-ext-install mysqli
# نسخ ملفات مشروعك من المجلد الحالي إلى داخل الحاوية
COPY . /var/www/html/
EXPOSE 80