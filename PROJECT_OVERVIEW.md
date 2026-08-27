# IHRP EC Online Submission

เอกสารนี้สรุปภาพรวมจากโค้ดใน repository เพื่อช่วยให้นักพัฒนาเข้าใจระบบและเริ่มทำงานได้เร็วขึ้น โดยไม่ได้แทนที่เอกสารข้อกำหนดทางธุรกิจอย่างเป็นทางการ

สำหรับ mental model, state machine, แผนที่ไฟล์ และแนวทางแก้ feature ให้ดู `docs/DEVELOPER_GUIDE.md`

## ภาพรวมระบบ

IHRP EC Online Submission เป็นเว็บแอปพลิเคชันสำหรับบริหารกระบวนการยื่นและพิจารณาโครงการวิจัยด้านจริยธรรม ตั้งแต่การลงทะเบียนนักวิจัย การสร้างโครงการและคำขอยื่น การตรวจเอกสาร การมอบหมายกรรมการ การจัดประชุม การบันทึกมติ ไปจนถึงการออกเอกสารผลพิจารณา

- Backend และ server-rendered UI: PHP + Yii Framework 2
- ฐานข้อมูล: ใช้ Yii Active Record และ migration (การตั้งค่าจริงอยู่ใน `config/db.php`)
- ภาษาและเวลา: `th-TH`, เขตเวลา `Asia/Bangkok`
- สิทธิ์ผู้ใช้: Yii RBAC แบบฐานข้อมูล
- Web root: `web/`
- Console entry point: `yii` หรือ `yii.bat`

## ความสามารถหลัก

### ผู้ใช้และสิทธิ์

- ลงทะเบียน เข้าสู่ระบบ ลืมรหัสผ่าน และเลือกบทบาท
- จัดการบุคคล หน่วยงาน แผนก ฝ่าย ตำแหน่ง และประเภทงาน
- กำหนด Role, Person Role, Panel และตำแหน่งกรรมการ
- ตรวจสอบสิทธิ์ผ่าน RBAC และรายการ permission ที่เพิ่มผ่าน migrations

### โครงการและการยื่นพิจารณา

- สร้างและแก้ไขข้อมูลโครงการ นักวิจัย ผู้ร่วมวิจัย และที่ปรึกษา
- รองรับการยื่นโครงการใหม่ การยื่นต่อเนื่อง และการยื่นแบบรับรอง
- แบ่งประเภทการพิจารณาเป็น Full Board, Expedited และ Exemption
- จัดการประเภทการยื่น แหล่งทุน อาสาสมัคร จำนวนอาสาสมัคร และข้อมูลเพิ่มเติม
- เก็บประวัติสถานะ การแก้ไข และเหตุการณ์ของ submission

### เอกสาร

- อัปโหลด ตรวจ อนุมัติ ดาวน์โหลด และรวมเอกสาร
- จัดการเอกสารของโครงการ กรรมการ และเอกสารผลพิจารณา
- สร้าง Word/PDF ด้วย PHPWord, phpdocx, mPDF และ HtmlToOpenXml
- มีระบบเข้ารหัสไฟล์และคำสั่งสำหรับล็อก PDF

### การพิจารณาและการประชุม

- มอบหมายเจ้าหน้าที่ ผู้ประสานงาน เลขานุการ และกรรมการ
- กรรมการตอบรับและทำแบบประเมิน
- จัดการห้องประชุม วาระ ผู้เข้าประชุม และคณะพิจารณา
- บันทึกมติและสร้างเอกสารผล ก่อนให้เลขานุการหรือประธานอนุมัติ

### แบบประเมินและการติดตาม

- แบบประเมินต่อเนื่อง (Continuing review)
- SAE และข้อมูลอาสาสมัครที่เกี่ยวข้อง
- Protocol deviation และประเภทเหตุการณ์
- แบบสอบถาม คำถาม ตัวเลือก และคำตอบ
- ประวัติการฝึกอบรม/CV และเงื่อนไขการอบรมตามประเภท submission

### งานสนับสนุน

- Email queue และ console command สำหรับประมวลผลอีเมล
- Alert และ notification history
- รายงาน สถิติ dashboard และการ export
- Audit log ผ่าน `bedezign/yii2-audit`
- API/JWT บางส่วนตามการตั้งค่าและ permission ในระบบ

## ลำดับงานหลักโดยย่อ

สถานะจริงถูกกำหนดใน `models/Submission.php` และมีรายละเอียดแยกตามประเภทคำขอ โดยภาพรวมเป็นดังนี้:

1. นักวิจัยสร้างโครงการและคำขอยื่น
2. เพิ่มผู้วิจัย/ที่ปรึกษา กรอกข้อมูล และแนบเอกสาร
3. ส่งคำขอและรอการรับรองจากผู้วิจัยที่เกี่ยวข้อง (ถ้ามี)
4. เจ้าหน้าที่ตรวจเอกสาร รับเรื่อง และสร้างรหัสโครงการ
5. กำหนดประเภทการพิจารณา เลขานุการ และกรรมการ
6. กรรมการตอบรับและทำแบบประเมิน
7. เพิ่มเรื่องเข้าวาระและดำเนินการประชุม
8. เจ้าหน้าที่และเลขานุการตรวจรับรองผล
9. ประธาน/เลขานุการอนุมัติเอกสารผลตาม workflow
10. เจ้าหน้าที่อัปโหลดหรือส่งเอกสารผล และปิดรอบการพิจารณา

## โครงสร้างโปรเจกต์

| Path | หน้าที่ |
| --- | --- |
| `assets/` | Yii asset bundles |
| `commands/` | Console commands เช่น email queue, alert, encryption และ PDF lock |
| `components/` | Component และ utility เฉพาะระบบ |
| `config/` | การตั้งค่า web, console, database และ parameters |
| `controllers/` | Web controllers และ application actions |
| `docs/` | เอกสารและแผนงานเฉพาะ feature |
| `mail/` | Template อีเมล |
| `messages/` | ข้อความแปลภาษา |
| `migrations/` | โครงสร้างฐานข้อมูล ข้อมูลตั้งต้น และ RBAC permissions |
| `models/` | Active Record, query, search และ form models |
| `rbac/` | Controller และ user class ที่เกี่ยวกับ RBAC |
| `tests/` | Codeception tests |
| `views/` | หน้าเว็บและ partial views |
| `web/` | Entry point, static assets, uploads และไฟล์ชั่วคราว |
| `widgets/` | UI widgets เฉพาะระบบ |

## กลุ่มโดเมนสำคัญ

- `Project*`: ข้อมูลโครงการ นักวิจัย ที่ปรึกษา คำถาม และประวัติรหัส
- `Submission*`: คำขอยื่น เอกสาร กรรมการ การแก้ไข สถานะ เหตุการณ์ และผล
- `Meeting*`, `Agenda*`: การประชุม ผู้เข้าประชุม วาระ และมติ
- `Person*`, `Role*`, `Panel*`: ผู้ใช้ บทบาท กรรมการ และคณะพิจารณา
- `Document*`, `ResultDocument*`: ชนิดเอกสารและเอกสารผล
- `ContinueAssessForm*`, `Sae*`, `Deviation*`: แบบประเมินและการติดตามหลังอนุมัติ
- `Organization`, `Department`, `Division`: โครงสร้างหน่วยงาน
- `Setting`, `RunningCode`, `EmailQueue`, `Alert`: การตั้งค่าและงานระบบ

## Dependencies ที่เด่น

- Yii 2, Yii Bootstrap และ SwiftMailer
- Kartik widgets, grid, export, date controls และ mPDF
- PHPWord, phpdocx และ HtmlToOpenXml สำหรับเอกสาร Office
- Guzzle สำหรับ HTTP client
- Endroid QR Code
- JWT (`sizeg/yii2-jwt`)
- FullCalendar และ Highcharts
- Codeception สำหรับการทดสอบ

ดูรายการและเวอร์ชันทั้งหมดได้จาก `composer.json` และ `composer.lock`

## การติดตั้งสำหรับพัฒนา

ข้อกำหนดจริงอาจขึ้นกับ environment เดิมขององค์กร แม้ `composer.json` จะระบุ PHP `>=5.4` แต่ Dockerfile ที่มีอยู่ใช้ PHP 7.3 และ dependency บางตัวอาจมีข้อกำหนดสูงกว่า ควรยึด `composer.lock` และทดสอบกับ environment เป้าหมาย

1. ติดตั้ง PHP extensions ที่ Yii และ libraries ด้านเอกสารต้องใช้
2. ติดตั้ง dependencies:

   ```bash
   composer install
   ```

3. สร้างฐานข้อมูล และกำหนด connection ใน `config/db.php`
4. ตรวจค่า secret และ environment-specific config ใน `config/` ห้าม commit credential จริง
5. รัน migration:

   ```bash
   php yii migrate
   ```

6. ให้ web server ชี้ document root ไปที่ `web/` หรือใช้ development server:

   ```bash
   php yii serve
   ```

7. เปิด URL ที่คำสั่งแสดง (โดยทั่วไป `http://localhost:8080`)

โปรเจกต์มี `.htaccess` สำหรับ deployment ใต้ path `/ihrp/`; หากติดตั้งคนละ path ต้องปรับ `RewriteBase` หรือ virtual host ให้สอดคล้องกัน

## คำสั่งที่ใช้บ่อย

```bash
# แสดงคำสั่ง console ทั้งหมด
php yii help

# อัปเดตฐานข้อมูล
php yii migrate

# รัน test suite
vendor/bin/codecept run
```

Console controllers เฉพาะระบบอยู่ใน `commands/` ควรใช้ `php yii help <command>` เพื่อตรวจ arguments ก่อนรัน โดยเฉพาะคำสั่งที่ส่งอีเมล เข้ารหัสไฟล์ หรือแก้ไข PDF

## จุดที่ควรระวัง

- `config/` อาจมี database, SMTP, JWT หรือ encryption secrets; ห้ามนำค่าเหล่านี้ไปใส่ในเอกสารหรือ log
- `web/tmp/`, uploads และไฟล์เอกสารที่ generate แล้วไม่ใช่ source code และอาจมีข้อมูลส่วนบุคคล
- `vendor/` ถูกเก็บอยู่ใน working tree บางส่วน; ไม่ควรแก้ dependency โดยตรงหากแก้ผ่าน Composer หรือ package ต้นทางได้
- แอปมี dependencies และรูปแบบ Yii 2 รุ่นเก่า ควรทดสอบ regression ก่อนอัปเกรด PHP/Yii หรือ Composer packages
- Workflow และสิทธิ์กระจายอยู่ใน controllers, models และ migrations; การเปลี่ยนสถานะต้องตรวจทั้ง UI, RBAC, email/alert และการสร้างเอกสาร
- โค้ดเกี่ยวข้องกับข้อมูลวิจัยและข้อมูลส่วนบุคคล ควรจำกัดการเข้าถึงไฟล์ upload, log, backup และฐานข้อมูลตามนโยบายองค์กร

## จุดเริ่มต้นสำหรับนักพัฒนา

- เริ่มต้นระบบและ routes: `config/web.php`
- Workflow หลัก: `controllers/SubmissionController.php`
- สถานะและความสัมพันธ์ของคำขอ: `models/Submission.php`
- ข้อมูลโครงการ: `models/Project.php`
- การประชุมและวาระ: `controllers/MeetingController.php`, `controllers/AgendaController.php`
- ผู้ใช้และบทบาท: `models/Person.php`, `models/User.php`, `rbac/`
- Schema และ permission history: `migrations/`
- แผนปรับปรุง CV/GCP: `docs/plans/cv-gcp-enhancement.md`

## ขอบเขตของเอกสารนี้

สรุปนี้สร้างจาก static inspection ของ source code ไม่ได้เชื่อมต่อฐานข้อมูล ไม่ได้รัน workflow ผ่านหน้าเว็บ และไม่ได้ยืนยัน business rule กับผู้ใช้งานจริง ดังนั้นชื่อสถานะ รายงาน และสิทธิ์บางส่วนควรตรวจสอบกับ environment และเจ้าของระบบก่อนนำไปใช้อ้างอิงเชิงปฏิบัติการ
