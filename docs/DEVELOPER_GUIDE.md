# IHRP Developer Guide

เอกสารนี้เป็น working knowledge สำหรับการพัฒนาระบบต่อจาก source code ปัจจุบัน อ่านภาพรวมผลิตภัณฑ์และวิธีติดตั้งได้ที่ `PROJECT_OVERVIEW.md`

## เริ่มงานบนเครื่องใหม่หรือ AI session ใหม่

เมื่อ clone หรือย้าย repository ไปเครื่องใหม่ ให้เริ่มต้นด้วยข้อความนี้:

```text
อ่าน PROJECT_OVERVIEW.md และ docs/DEVELOPER_GUIDE.md ให้ครบก่อน
จากนั้นตรวจ git status และ source code ที่เกี่ยวข้องก่อนเริ่มแก้ไข
อย่าเปิดเผย secret และอย่าแก้ไฟล์ที่ไม่เกี่ยวข้องกับงาน
```

ลำดับเริ่มต้นที่แนะนำ:

1. ตรวจว่า checkout branch และ commit ถูกต้อง
2. อ่าน `PROJECT_OVERVIEW.md` และเอกสารนี้
3. ตรวจ `git status` เพื่อแยกงานเดิมของผู้ใช้ออกจากงานใหม่
4. อ่าน controller, model, view และ migration ที่เกี่ยวข้องกับ feature
5. ตรวจ PHP/Yii/database version ของ environment ก่อนติดตั้งหรืออัปเดต dependency

ข้อมูลที่ต้องส่งแยกจาก Git:

- database, SMTP, JWT และ encryption secrets
- test account passwords
- production URLs, VPN และ network access
- certificates, signing keys และไฟล์ข้อมูลจริง

เก็บข้อมูลเหล่านี้ใน password manager หรือ secret manager ขององค์กร ไม่ควรใส่ใน Markdown, source code, commit หรือข้อความที่จะแชร์ต่อ

## Mental model

หน่วยธุรกิจหลักไม่ใช่ `Submission` เพียงอย่างเดียว แต่เป็นสายความสัมพันธ์ดังนี้:

```text
Person/User
   │
   ├── PersonRole ── Role / Panel
   │
   └── Project ── ProjectResearcher / ProjectConsultant
          │
          └── Submission ── SubmissionType / documents / volunteers / events
                 │
                 ├── SubmissionCommittee ── assessments / committee documents
                 │
                 ├── MeetingAgenda ── Meeting / Agenda / Resolution
                 │
                 ├── SubmissionResultDocument
                 │
                 └── SubmissionStatusHistory
```

- `Project` คือข้อมูลวิจัยระยะยาว หนึ่งโครงการมี submission ได้หลายรอบ
- `Submission` คือธุรกรรมการยื่นแต่ละครั้ง ทั้งโครงการใหม่ การติดตาม การแก้ไข และการยื่นซ้ำ
- `ref_submission_id` เชื่อม submission รุ่นถัดไปกับรอบอ้างอิง จึงต้องระวังเมื่อลบหรือเปลี่ยนมติย้อนหลัง
- `SubmissionTypeGroup` แบ่งกลุ่มใหญ่เป็น New, Continuing และ Resubmit
- `SubmissionType` ระบุชนิดงานละเอียด เช่น progress, renewal, amendment, SAE, deviation และ closing
- การประชุมเชื่อม submission ผ่าน `MeetingAgenda`; มติและเอกสารผลจึงไม่ได้อยู่ใน `Meeting` โดยตรงทั้งหมด

## Actors และสิทธิ์

Role IDs ที่อ้างในโค้ด (`models/Role.php`):

| ID | Constant | หน้าที่โดยทั่วไป |
| ---: | --- | --- |
| 1 | `ADMIN` | ดูแลระบบและข้อมูลตั้งต้น |
| 2 | `RESEARCHER` | สร้างโครงการและยื่นเอกสาร |
| 3 | `COMMITTEE` | ตอบรับและประเมินโครงการ |
| 4 | `COPRESIDENT` | รองประธาน/ผู้ทำหน้าที่ร่วม |
| 5 | `SECRETARY` | เลือกกรรมการและตรวจผล |
| 6 | `PRESIDENT` | ตรวจ/อนุมัติผลในขั้นประธาน |
| 7 | `STAFF` | ตรวจเอกสาร จัด workflow และประชุม |
| 8 | `COORDINATOR` | ประสานงานและเตรียมคำขอ |
| 11 | `WEB_API` | การเข้าถึงผ่าน API |

อย่าสรุปสิทธิ์จาก Role ID เพียงอย่างเดียว ระบบใช้ Yii RBAC แบบ DB และมี permission เพิ่ม/แก้ผ่าน migrations อีกทั้งบุคคลหนึ่งมีหลาย `PersonRole` และบาง role ผูกกับ `Panel`

## Submission state machine

สถานะหลักเรียงตาม flow ปกติ:

| Code | Constant | ความหมายโดยย่อ |
| ---: | --- | --- |
| 100 | `STATUS_PENDING_SUBMISSION` | เตรียมคำขอ |
| 190 | `STATUS_WAITING_APPROVE_PROJECT_RESEARCHER` | รอหัวหน้า/ผู้วิจัยยืนยัน |
| 200/250 | `STATUS_SUBMITTED` / `STATUS_SUBMITTED_CON` | ยื่นแล้ว |
| 300 | `STATUS_DOC_APPROVED` | เจ้าหน้าที่ตรวจเอกสาร |
| 400 | `STATUS_CODE_GENERATED` | สร้างเลขโครงการ |
| 500 | `STATUS_MEETING_APPOINTMENT` | กำหนดประชุม |
| 550 | `STATUS_SECRETARY_SELECT_TYPE` | รอเลือกประเภทพิจารณา |
| 600 | `STATUS_SECRETARY_SELECTED` | เลือกเลขานุการแล้ว |
| 700 | `STATUS_COMMITTEE_SELECTED` | เลือกกรรมการแล้ว |
| 800 | `STATUS_COMMITTEE_ACCEPTED` | กรรมการตอบรับครบ |
| 900 | `STATUS_COMMITTEE_ASSESSED` | ประเมินครบ |
| 1000 | `STATUS_AGENDA_ADDED` | บรรจุวาระ |
| 1100 | `STATUS_MEETING_DONE` | ประชุมพิจารณาแล้ว |
| 1150–1200 | approval states | ตรวจรายงาน/มติ |
| 1260/1280 | result approval states | ประธาน/เลขานุการตรวจเอกสารผล |
| 1300 | `STATUS_STAFF_UPLOAD_RESULTDOCUMENT` | ส่งผลให้นักวิจัยแล้ว |

เส้นทางผิดปกติที่ต้องรองรับ ได้แก่ รอแก้ไข ถูกตีกลับ กรรมการไม่รับงาน เปลี่ยน panel และ resubmission ดังนั้นห้ามเปลี่ยน `status` โดยตรงโดยไม่ตรวจ side effects

`SubmissionStatusHistoryBehavior` บันทึกประวัติหลัง insert/update แต่ flow บางจุดอาจมี email, alert, document generation และ timestamp เพิ่มเติมอยู่ใน controller ด้วย

## มติ

ค่ามติหลักใน `Submission`:

| Code | ความหมายจาก label ในระบบ |
| --- | --- |
| `Y` | รับรอง/รับทราบ ตามชนิด submission |
| `C` | รับรองหลังแก้ไขตามมติ |
| `R` | ขอข้อมูลเพิ่มและนำกลับมาพิจารณาใหม่ |
| `N` | ไม่รับรอง |
| `W` | ถอนออกจากการพิจารณา |
| `T` | ยุติการดำเนินการ |
| `P` | เปลี่ยน Panel |

ข้อความแสดงผลของ `Y/C/N/W/T` ขึ้นกับ `SubmissionType::resolution_label` จึงไม่ควร hard-code คำแปลในหน้าใหม่

## จุดที่ business logic อาศัยอยู่

| งาน | ไฟล์หลักที่ต้องอ่านร่วมกัน |
| --- | --- |
| สร้าง/แก้/ส่งคำขอ | `controllers/SubmissionController.php`, `models/Submission.php`, `views/submission/` |
| โครงการและทีมวิจัย | `ProjectController.php`, `ProjectResearcherController.php`, model ที่ชื่อเดียวกัน |
| ตรวจและรวมเอกสาร | `SubmissionDocumentController.php`, `SubmissionDocument.php`, `DocumentStamper.php`, `PdfLocker.php` |
| เลือก/ประเมินกรรมการ | `SubmissionCommitteeController.php`, `QuestionnaireAnswerController.php`, models ที่เกี่ยวข้อง |
| ประชุมและวาระ | `MeetingController.php`, `MeetingAgendaController.php`, `Meeting.php`, `MeetingAgenda.php` |
| เอกสารผล | `ResultDocumentController.php`, `SubmissionResultDocumentController.php`, `EsignService.php` |
| ผู้ใช้/บทบาท/Panel | `PersonController.php`, `PersonRoleController.php`, `rbac/`, `models/Role.php` |
| CV/GCP/training | `PersonTrainingController.php`, `Person.php`, `SubmissionTypeTrainingRequirement.php` |
| Email/alert | `models/EmailQueue.php`, `commands/EmailQueueController.php`, `models/Alert.php` |
| CREC/API | `components/Crec.php`, `components/ExternalApi.php`, JWT config และ permission migrations |

ชื่อไฟล์ controller ในตารางอ้างจากโฟลเดอร์ `controllers/`

## รูปแบบโค้ดที่ใช้ซ้ำ

- Active Record แต่ละตัวมักมี `*Query.php` และ entity ที่ใช้หน้าค้นหามักมี `*Search.php`
- การลบจำนวนมากเป็น soft delete ผ่าน field `deleted`; ตรวจ query scope `isDeleted(false)` ก่อนเพิ่ม query ใหม่
- `TimestampBehavior`/`BlameableBehavior` ถูกใช้ในหลาย model สำหรับ `created_at`, `updated_at`, `created_by`, `updated_by`
- UI เป็น server-rendered Yii views และใช้ Kartik widgets/PJAX จำนวนมาก การแก้ controller response จึงอาจกระทบ partial reload
- ข้อความผู้ใช้ผ่าน `Yii::t('app', ...)`; ระบบตั้งภาษาไทยเป็นค่าเริ่มต้น
- เอกสาร Word/PDF ใช้ template และไฟล์ชั่วคราว การแก้ชื่อ path หรือ output format ต้องตรวจทั้ง download, preview, encryption และ cleanup

## แนวทางก่อนแก้ feature

1. ระบุ actor, submission group/type, panel และสถานะที่ feature มีผล
2. หา action ต้นทางใน controller และ view/form ที่เรียก action นั้น
3. อ่าน model rules ตาม scenario รวมถึง query scopes ที่ controller ใช้
4. ตรวจ relation และ records ลูก เช่น documents, committee, meeting agenda และ status history
5. ค้น permission เดิมใน migrations และ RBAC ก่อนสร้าง permission ใหม่
6. ตรวจ side effects: email queue, alerts, file generation, timestamps และ external API
7. ทดสอบทั้ง happy path และเส้นทางตีกลับ/แก้ไข/resubmit

## Technical risks ที่ทราบจาก static inspection

- `SubmissionController.php` มีขนาดประมาณ 290 KB และ `Submission.php` ประมาณ 135 KB เป็นจุดรวม logic และมี regression risk สูง
- test suite ที่พบยังเป็น test ตัวอย่างของ Yii basic template เป็นหลัก จึงไม่ครอบคลุม domain workflow ที่สำคัญ
- dependency versions ผสมทั้ง package เก่า, wildcard และ `dev-master`; หลีกเลี่ยง `composer update` แบบกว้างโดยไม่มี lockfile review
- มี config backup หลายชุด (`*.bak`, `*.save`, `*-1904.php`) ซึ่งเสี่ยงต่อ configuration drift และ secret leakage
- มี generated/uploaded documents ภายใน `web/` และ `web/tmp/`; ต้องถือว่าอาจเป็นข้อมูลอ่อนไหว
- model/controller บางส่วนผูก presentation, query และ business rules เข้าด้วยกัน ทำให้ unit test และ refactor ยาก
- status label มีหลายชุดตาม CREC/local flow; ต้องเลือก method ให้ตรงบริบท ไม่ควรสร้าง mapping ซ้ำ

## Testing strategy สำหรับงานถัดไป

เนื่องจาก automated coverage ต่ำ ให้ใช้ลำดับนี้:

1. เพิ่ม characterization test รอบ method/query ที่จะแก้ ถ้าสามารถสร้าง fixture ได้
2. รัน syntax check กับไฟล์ PHP ที่เปลี่ยน (`php -l`)
3. รัน Codeception suite ที่เกี่ยวข้อง
4. ทดสอบ route จริงด้วย role ที่เกี่ยวข้องอย่างน้อยหนึ่ง role
5. ตรวจฐานข้อมูลก่อน/หลัง โดยเฉพาะ `submission`, status history และ child records
6. ตรวจ email queue/alert และไฟล์ผลลัพธ์เมื่อ workflow มี side effects

## หลักการทำงานร่วมกันใน repository นี้

- รักษา compatibility กับ PHP/Yii version ของ environment จริงจนกว่าจะมีแผนอัปเกรดชัดเจน
- ไม่แก้ `vendor/` โดยตรง ยกเว้นมีเหตุผลและแผน patch ที่บันทึกไว้
- ไม่เผยแพร่ค่าจาก `config/db.php`, SMTP, JWT หรือ encryption keys
- ไม่ลบไฟล์ใน uploads/tmp โดยถือว่าเป็นของทิ้ง จนกว่าจะยืนยัน lifecycle และ backup
- การเปลี่ยน workflow ต้องระบุ transition เดิม/ใหม่, actor, permission และ side effects ใน PR/บันทึกงาน
- แยก refactor ออกจาก behavior change เมื่อทำได้ เพื่อลดความเสี่ยงของ legacy flow

## สิ่งที่ยังต้องยืนยันกับเจ้าของระบบ

- PHP/Apache/DB versions และ deployment topology ที่ production ใช้จริง
- แหล่งที่มาของ environment secrets และขั้นตอน deploy
- นิยาม role ทางธุรกิจและผู้อนุมัติในแต่ละ panel
- ความแตกต่างที่ตั้งใจไว้ระหว่าง CREC กับ local KKU flow
- document retention, encryption และข้อมูลส่วนบุคคล
- cron/scheduler ที่เรียก email, alert หรือ maintenance commands
- ชุดข้อมูลหรือ sandbox account สำหรับ regression testing
