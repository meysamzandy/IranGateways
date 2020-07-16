# Iran Gateways

Package description: درگاه پرداخت بانک های ایرانی برای لاراول

## Installation

نصب از طریق کامپوزر
```bash
composer require meysam-znd/iran-gateways
```

### Publish package assets

```bash
php artisan vendor:publish --provider="MeysamZnd\IranGateways\ServiceProvider"
```

## Usage
این پکیج فعلا شامل درگاه پرداخت بانک ملت می باشد.

##### راهنمای استفاده
1- ابتدا یک شئ از کلاس بانک ملت با استفاده از اطلاعات هویتی خود ایجاد کنید.
```bash
/**
 * @param intiger $terminal : آی دی ترمینال بانک ملت
 * @param string $username : نام کاربری بانک ملت
 * @param string $password : رمز عبور بانک ملت
 */
$mellat = new MellatBank($terminal, $username, $password);
```
2- سپس برای شروع پرداخت، متد پرداخت را با استفاده از داده های مورد نظر فراخوانی کنید تا به درگاه بانک ملت هدایت شوید.

```bash
/**
 * @param  $amount : مبلغ پرداخت
 * @param  $callBackUrl : آدرس برگشت بعد از پرداخت
 * @param $orderId : شماره فاکتور
 */
$mellat->payment($amount, $orderId, $callBackUrl);
```
3- بعد از پرداخت در صفحه بازگشت از درگاه که آدرس آن را در مرحله دوم وارد کرده بودید میتوانید نتیجه پرداخت را با استفاده از متد زیر برررسی کنید.
```bash

$results = $mellat->controlPayment($_POST);
if (!($results && $results['status'] === 'success')) {
        dd($results);
   }
dd($results['trans');
```
