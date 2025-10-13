# Doctor Appointment System - Database Design & Documentation

## Table of Contents
1. [EER Relational Model](#eer-relational-model)
2. [Use Cases](#use-cases)
3. [Conflicts & Constraints](#conflicts--constraints)
4. [Schedules & Workflows](#schedules--workflows)

---

## EER Relational Model

### Entities and Relationships

#### 1. **User (Superclass)**
```
User
├── id (PK)
├── name
├── email (UNIQUE)
├── password
├── phone
├── address
├── date_of_birth
├── gender
├── role (ENUM: 'admin', 'doctor', 'patient')
├── created_at
└── updated_at
```

#### 2. **Administrator (Specialization of User)**
```
Administrator
├── user_id (PK, FK → User.id)
├── permissions (JSON)
├── department
└── employee_id (UNIQUE)
```

#### 3. **Doctor (Specialization of User)**
```
Doctor
├── user_id (PK, FK → User.id)
├── license_number (UNIQUE)
├── specialization_id (FK → Specialization.id)
├── years_of_experience
├── consultation_fee
├── biography
├── education
├── status (ENUM: 'active', 'inactive', 'on_leave')
└── rating (DECIMAL)
```

#### 4. **Patient (Specialization of User)**
```
Patient
├── user_id (PK, FK → User.id)
├── blood_type
├── allergies (TEXT)
├── emergency_contact_name
├── emergency_contact_phone
├── insurance_provider
└── insurance_policy_number
```

#### 5. **Specialization**
```
Specialization
├── id (PK)
├── name (UNIQUE)
├── description
├── created_at
└── updated_at
```

#### 6. **Appointment**
```
Appointment
├── id (PK)
├── patient_id (FK → Patient.user_id)
├── doctor_id (FK → Doctor.user_id)
├── schedule_id (FK → DoctorSchedule.id)
├── appointment_date
├── appointment_time
├── duration (minutes)
├── status (ENUM: 'pending', 'confirmed', 'completed', 'cancelled', 'no_show')
├── reason
├── notes
├── created_by (FK → User.id)
├── created_at
└── updated_at
```

#### 7. **DoctorSchedule**
```
DoctorSchedule
├── id (PK)
├── doctor_id (FK → Doctor.user_id)
├── day_of_week (ENUM: 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
├── start_time
├── end_time
├── slot_duration (minutes, default: 30)
├── max_patients_per_slot
├── is_available (BOOLEAN)
├── effective_from (DATE)
├── effective_until (DATE, NULL for ongoing)
├── created_at
└── updated_at
```

#### 8. **MedicalRecord**
```
MedicalRecord
├── id (PK)
├── patient_id (FK → Patient.user_id)
├── doctor_id (FK → Doctor.user_id)
├── appointment_id (FK → Appointment.id, NULL if general record)
├── diagnosis
├── prescription
├── lab_results (TEXT)
├── notes
├── attachments (JSON)
├── created_at
└── updated_at
```

#### 9. **Payment**
```
Payment
├── id (PK)
├── appointment_id (FK → Appointment.id)
├── patient_id (FK → Patient.user_id)
├── amount
├── payment_method (ENUM: 'cash', 'card', 'insurance', 'online')
├── payment_status (ENUM: 'pending', 'completed', 'refunded', 'failed')
├── transaction_id (UNIQUE)
├── paid_at
├── created_at
└── updated_at
```

#### 10. **Notification**
```
Notification
├── id (PK)
├── user_id (FK → User.id)
├── type (ENUM: 'appointment_reminder', 'appointment_confirmed', 'appointment_cancelled', 'system')
├── title
├── message
├── is_read (BOOLEAN, default: false)
├── related_appointment_id (FK → Appointment.id, NULL)
├── created_at
└── updated_at
```

#### 11. **Review**
```
Review
├── id (PK)
├── doctor_id (FK → Doctor.user_id)
├── patient_id (FK → Patient.user_id)
├── appointment_id (FK → Appointment.id)
├── rating (1-5)
├── comment
├── is_approved (BOOLEAN, default: false)
├── created_at
└── updated_at
```

### ER Diagram Relationships

```
User (1) ──ISA──> (1) Administrator
User (1) ──ISA──> (1) Doctor
User (1) ──ISA──> (1) Patient

Specialization (1) ────< (N) Doctor
Doctor (1) ────< (N) DoctorSchedule
Doctor (1) ────< (N) Appointment
Patient (1) ────< (N) Appointment
Doctor (1) ────< (N) MedicalRecord
Patient (1) ────< (N) MedicalRecord
Appointment (1) ───── (0..1) MedicalRecord
Appointment (1) ───── (0..1) Payment
User (1) ────< (N) Notification
Doctor (1) ────< (N) Review
Patient (1) ────< (N) Review
Appointment (1) ───── (0..1) Review
DoctorSchedule (1) ────< (N) Appointment
```

---

## Use Cases

### Administrator Use Cases

#### UC-A01: Manage Users
**Actor:** Administrator
**Description:** Administrator can create, read, update, and delete user accounts (doctors, patients, other admins)
**Preconditions:** Administrator is authenticated
**Main Flow:**
1. Administrator navigates to user management dashboard
2. System displays list of all users with filters (role, status, search)
3. Administrator selects an action (create/edit/delete/view)
4. System validates permissions
5. Administrator inputs/modifies user information
6. System validates data and saves changes
7. System sends notification to affected user (if applicable)

**Alternate Flows:**
- A1: Validation fails → System displays error messages
- A2: User email already exists → System prevents duplicate creation

---

#### UC-A02: Manage Doctor Schedules
**Actor:** Administrator
**Description:** Administrator can create and modify doctor availability schedules
**Preconditions:** Administrator is authenticated, Doctor exists in system
**Main Flow:**
1. Administrator selects doctor from list
2. System displays current schedule
3. Administrator adds/modifies time slots
4. System checks for conflicts with existing appointments
5. Administrator confirms changes
6. System updates schedule and notifies doctor
7. System sends notifications to affected patients (if appointments need rescheduling)

**Postconditions:** Doctor schedule is updated in database

---

#### UC-A03: View System Reports
**Actor:** Administrator
**Description:** Generate reports on appointments, revenue, doctor performance
**Preconditions:** Administrator is authenticated
**Main Flow:**
1. Administrator selects report type and date range
2. System generates report with statistics and charts
3. Administrator can export to PDF/Excel
4. System logs report generation

---

#### UC-A04: Manage Specializations
**Actor:** Administrator
**Description:** Add, edit, or remove medical specializations
**Preconditions:** Administrator is authenticated
**Main Flow:**
1. Administrator navigates to specializations management
2. System displays current specializations
3. Administrator creates/edits specialization
4. System validates uniqueness
5. System saves changes

**Business Rules:**
- Cannot delete specialization if assigned to active doctors

---

### Doctor Use Cases

#### UC-D01: Manage Personal Schedule
**Actor:** Doctor
**Description:** Doctor can view and request changes to their availability schedule
**Preconditions:** Doctor is authenticated
**Main Flow:**
1. Doctor navigates to schedule management
2. System displays current weekly schedule
3. Doctor requests time slot changes (add/remove/modify)
4. System checks for existing appointments in affected slots
5. System creates change request for administrator approval
6. Administrator approves/rejects request
7. System notifies doctor of decision

**Alternate Flows:**
- A1: No appointments conflict → Changes applied immediately
- A2: Appointments exist → System requires administrator approval

---

#### UC-D02: View Appointments
**Actor:** Doctor
**Description:** Doctor views their upcoming and past appointments
**Preconditions:** Doctor is authenticated
**Main Flow:**
1. Doctor accesses appointments dashboard
2. System displays appointments (today, upcoming, past)
3. Doctor can filter by date, status, patient
4. Doctor selects appointment to view details
5. System displays patient info and appointment notes

**Postconditions:** Appointment data is displayed

---

#### UC-D03: Update Appointment Status
**Actor:** Doctor
**Description:** Doctor confirms, completes, or cancels appointments
**Preconditions:** Doctor is authenticated, Appointment exists
**Main Flow:**
1. Doctor selects appointment
2. Doctor updates status (confirmed/completed/cancelled)
3. If completed, doctor can add notes/diagnosis
4. System validates status change
5. System updates appointment
6. System sends notification to patient

**Business Rules:**
- Cannot mark future appointments as completed
- Cancellation within 24 hours requires administrator approval

---

#### UC-D04: Manage Medical Records
**Actor:** Doctor
**Description:** Doctor creates and updates patient medical records
**Preconditions:** Doctor is authenticated, Patient has appointment
**Main Flow:**
1. Doctor accesses patient's medical history
2. System displays existing records
3. Doctor creates new record or updates existing
4. Doctor enters diagnosis, prescription, lab results
5. Doctor can attach files (images, PDFs)
6. System validates and saves record
7. System timestamps and logs the entry

**Security:**
- Only doctors who have seen the patient can access records
- All access is logged for audit

---

#### UC-D05: View Patient Reviews
**Actor:** Doctor
**Description:** Doctor views ratings and reviews from patients
**Preconditions:** Doctor is authenticated
**Main Flow:**
1. Doctor navigates to reviews section
2. System displays approved reviews and average rating
3. Doctor can view individual comments and ratings
4. System shows trends over time

---

### Patient Use Cases

#### UC-P01: Register Account
**Actor:** Patient (Unauthenticated)
**Description:** New patient creates an account
**Preconditions:** None
**Main Flow:**
1. Patient clicks "Register" on homepage
2. System displays registration form
3. Patient enters personal information
4. System validates data (email uniqueness, password strength)
5. Patient verifies email address
6. System creates patient account
7. System sends welcome email

**Postconditions:** Patient account is created and can log in

---

#### UC-P02: Search for Doctors
**Actor:** Patient
**Description:** Patient searches for doctors by specialization, name, or rating
**Preconditions:** Patient is authenticated
**Main Flow:**
1. Patient accesses doctor search
2. System displays specializations and search filters
3. Patient selects specialization and/or enters search criteria
4. System displays matching doctors with ratings, fees, availability
5. Patient can view doctor profiles
6. Patient selects doctor to book appointment

---

#### UC-P03: Book Appointment
**Actor:** Patient
**Description:** Patient schedules an appointment with a doctor
**Preconditions:** Patient is authenticated, Doctor has available slots
**Main Flow:**
1. Patient selects doctor
2. System displays doctor's available time slots
3. Patient selects preferred date and time
4. Patient enters reason for visit
5. System validates slot availability (double-check)
6. System creates appointment with "pending" status
7. System sends confirmation to patient and notification to doctor
8. Doctor/Admin confirms appointment
9. System updates status to "confirmed" and notifies patient

**Alternate Flows:**
- A1: Slot becomes unavailable → System suggests alternative times
- A2: Doctor requires admin approval → Appointment stays pending until approved

---

#### UC-P04: View Appointments
**Actor:** Patient
**Description:** Patient views their appointment history and upcoming appointments
**Preconditions:** Patient is authenticated
**Main Flow:**
1. Patient accesses appointments section
2. System displays appointments (upcoming, past, cancelled)
3. Patient can view appointment details
4. For upcoming appointments, patient can see doctor info and time remaining

---

#### UC-P05: Cancel Appointment
**Actor:** Patient
**Description:** Patient cancels an upcoming appointment
**Preconditions:** Patient is authenticated, Appointment exists and is not in the past
**Main Flow:**
1. Patient selects appointment to cancel
2. System shows cancellation policy
3. Patient confirms cancellation
4. System updates appointment status to "cancelled"
5. System notifies doctor/administrator
6. System processes refund if applicable

**Business Rules:**
- Cancellations within 24 hours may incur fees
- Cancelled slots become available for other patients

---

#### UC-P06: View Medical Records
**Actor:** Patient
**Description:** Patient views their own medical history
**Preconditions:** Patient is authenticated
**Main Flow:**
1. Patient navigates to medical records
2. System displays all medical records in chronological order
3. Patient can filter by doctor or date
4. Patient can view diagnoses, prescriptions, lab results
5. Patient can download records as PDF

**Privacy:**
- Patients can only view their own records

---

#### UC-P07: Make Payment
**Actor:** Patient
**Description:** Patient pays for appointment services
**Preconditions:** Patient is authenticated, Appointment is completed
**Main Flow:**
1. Patient views pending payments
2. System displays amount due and appointment details
3. Patient selects payment method
4. System processes payment (integration with payment gateway)
5. System generates receipt
6. System updates payment status
7. System sends receipt to patient email

**Alternate Flows:**
- A1: Payment fails → System allows retry or alternative method

---

#### UC-P08: Leave Review
**Actor:** Patient
**Description:** Patient rates and reviews a doctor after appointment
**Preconditions:** Patient is authenticated, Appointment is completed
**Main Flow:**
1. Patient navigates to completed appointment
2. System prompts for review (if not given)
3. Patient provides rating (1-5 stars) and optional comment
4. System saves review as "pending approval"
5. Administrator reviews and approves
6. System publishes review and updates doctor's average rating
7. System may notify doctor

**Business Rules:**
- One review per appointment
- Reviews moderated by admin before publication

---

## Conflicts & Constraints

### 1. Scheduling Conflicts

#### SC-01: Double Booking Prevention
**Conflict:** Two patients booking the same time slot
**Resolution:**
- Database constraint: UNIQUE(doctor_id, appointment_date, appointment_time)
- Application-level locking during booking process
- Real-time availability check before finalizing appointment
- Transaction isolation to prevent race conditions

**Implementation:**
```sql
-- Constraint in appointments table
CONSTRAINT no_double_booking UNIQUE(doctor_id, appointment_date, appointment_time, status)
WHERE status NOT IN ('cancelled', 'no_show')
```

---

#### SC-02: Doctor Schedule Overlap
**Conflict:** Doctor assigned multiple overlapping schedules
**Resolution:**
- Validation before saving schedule
- Check for time range overlaps on same day
- Prevent overlapping effective date ranges

**Validation Logic:**
```
For new schedule entry:
1. Query existing schedules for same doctor and day_of_week
2. Check if new time range overlaps with existing (start_time < existing.end_time AND end_time > existing.start_time)
3. Check effective date ranges don't overlap
4. Reject if conflict found
```

---

#### SC-03: Appointment Outside Schedule
**Conflict:** Booking appointment when doctor is not available
**Resolution:**
- Cross-reference DoctorSchedule before allowing booking
- Only show available slots in UI
- Backend validation rejects appointments outside schedule

---

### 2. Data Integrity Conflicts

#### DI-01: User Role Consistency
**Conflict:** User having multiple role entries (e.g., both doctor and patient)
**Resolution:**
- Database constraint: User can only have ONE specialization entry
- Separate tables enforce one-to-one relationship
- Application logic prevents creating multiple role entries

---

#### DI-02: Orphaned Appointments
**Conflict:** Deleting doctor/patient who has appointments
**Resolution:**
- Soft delete approach: Mark users as "inactive" instead of deleting
- Foreign key constraint with ON DELETE RESTRICT
- Admin must reassign or cancel appointments before deactivation

**Implementation:**
```sql
-- Foreign key constraints
ALTER TABLE appointments
  ADD CONSTRAINT fk_doctor
  FOREIGN KEY (doctor_id) REFERENCES doctors(user_id)
  ON DELETE RESTRICT;
```

---

#### DI-03: Payment Without Appointment
**Conflict:** Creating payment for non-existent appointment
**Resolution:**
- Foreign key constraint on appointment_id
- Validation: Appointment must be in "completed" status
- Amount must match doctor's consultation fee

---

### 3. Business Logic Conflicts

#### BL-01: Past Appointment Modification
**Conflict:** Attempting to edit/cancel past appointments
**Resolution:**
- Application-level validation checks appointment_date >= current_date
- Only "pending" and "confirmed" appointments can be cancelled
- "Completed" appointments can only have records added, not status changed

---

#### BL-02: Review Without Completed Appointment
**Conflict:** Patient leaving review without attending appointment
**Resolution:**
- Foreign key constraint links review to appointment
- Validation: Appointment status must be "completed"
- Patient must be the same as appointment patient

---

#### BL-03: Concurrent Status Updates
**Conflict:** Doctor and patient/admin updating appointment simultaneously
**Resolution:**
- Optimistic locking with version number
- Last-write-wins with audit trail
- Transaction isolation level: READ COMMITTED

**Implementation:**
```sql
-- Add version column to appointments
ALTER TABLE appointments ADD COLUMN version INT DEFAULT 0;

-- Update with version check
UPDATE appointments
SET status = ?, version = version + 1
WHERE id = ? AND version = ?;
```

---

### 4. Security & Privacy Conflicts

#### SP-01: Unauthorized Medical Record Access
**Conflict:** Doctor accessing patient records without authorization
**Resolution:**
- Access control: Doctor can only view records for their own patients
- Check: appointment exists with doctor_id AND patient_id
- All access logged in audit table

**Authorization Logic:**
```
hasAccessToMedicalRecord(doctorId, patientId):
  return Appointment.exists(doctor_id=doctorId, patient_id=patientId)
```

---

#### SP-02: Data Export Privacy
**Conflict:** Sensitive patient data in reports
**Resolution:**
- Admin reports anonymize patient data by default
- Full data requires special permission
- Audit logging of all data exports
- GDPR compliance: patients can request data deletion

---

### 5. Capacity Conflicts

#### CC-01: Overbooked Slots
**Conflict:** More patients than max_patients_per_slot
**Resolution:**
- Count existing appointments for time slot
- Reject booking if count >= max_patients_per_slot
- Cache available slot counts for performance

**Query:**
```sql
SELECT COUNT(*) FROM appointments
WHERE doctor_id = ?
  AND appointment_date = ?
  AND appointment_time = ?
  AND status IN ('pending', 'confirmed');
```

---

#### CC-02: Emergency Overbooking
**Conflict:** Need to fit emergency patient in full schedule
**Resolution:**
- Admin override capability
- Temporary increase of max_patients_per_slot
- Flag appointment as "emergency"
- Notify doctor of overbooked slot

---

## Schedules & Workflows

### Workflow 1: Patient Appointment Booking

```
[START] Patient Login
   ↓
Search Doctors (by specialization/name)
   ↓
View Doctor Profile & Reviews
   ↓
Check Available Slots
   ↓
Select Date & Time
   ↓
Enter Appointment Details (reason, notes)
   ↓
[SYSTEM] Validate Slot Availability
   ↓
   ├─→ [IF Unavailable] → Show Error → Back to Select Date & Time
   ↓
Create Appointment (status: pending)
   ↓
[SYSTEM] Send Notification to Doctor/Admin
   ↓
[WAIT] Doctor/Admin Confirms
   ↓
   ├─→ [IF Confirmed] → Update Status to "confirmed"
   │                   → Send Confirmation to Patient
   │                   → [OPTIONAL] Process Pre-Payment
   ↓
   └─→ [IF Rejected] → Update Status to "cancelled"
                      → Send Notification to Patient
                      → Suggest Alternative Times
   ↓
[END] Appointment Scheduled
```

**Timeline:**
- Patient action: 2-5 minutes
- Confirmation wait: 1-24 hours
- Auto-reminder: 24 hours before appointment

---

### Workflow 2: Doctor Consultation Process

```
[START] View Today's Appointments
   ↓
Patient Arrives (Check-in)
   ↓
[OPTIONAL] Admin/Receptionist Marks Arrival
   ↓
Doctor Reviews Patient History
   ↓
Conduct Consultation
   ↓
Update Medical Record
   ├─→ Enter Diagnosis
   ├─→ Write Prescription
   ├─→ Order Lab Tests
   └─→ Upload Documents/Images
   ↓
Mark Appointment as "completed"
   ↓
[SYSTEM] Generate Invoice
   ↓
Patient Makes Payment
   ↓
[SYSTEM] Send Receipt & Prescription to Patient Email
   ↓
[OPTIONAL] Patient Leaves Review
   ↓
[END] Consultation Complete
```

**Duration:**
- Pre-consultation prep: 5 minutes
- Consultation: 15-45 minutes
- Record update: 5-10 minutes
- Total per patient: 30-60 minutes

---

### Workflow 3: Doctor Schedule Management

```
[START] Doctor Requests Schedule Change
   ↓
Doctor Submits New Availability
   ↓
[SYSTEM] Check for Existing Appointments
   ↓
   ├─→ [IF No Conflicts] → Auto-approve
   │                      → Update Schedule
   │                      → Notify Doctor
   │                      → [END]
   ↓
   └─→ [IF Conflicts Exist] → Flag for Admin Review
                             ↓
                         Administrator Reviews Request
                             ↓
                             ├─→ [IF Approved] → Contact Affected Patients
                             │                  → Reschedule Appointments
                             │                  → Update Doctor Schedule
                             │                  → Notify Doctor & Patients
                             ↓
                             └─→ [IF Rejected] → Notify Doctor
                                                → Suggest Alternatives
   ↓
[END] Schedule Updated
```

**Timeline:**
- Doctor request: Anytime
- Auto-approval: Immediate
- Admin review: 1-3 business days
- Patient rescheduling: 3-7 days

---

### Workflow 4: Appointment Reminder System

```
[CRON JOB] Daily at 9:00 AM
   ↓
Query Appointments (date = tomorrow AND status = 'confirmed')
   ↓
For Each Appointment:
   ↓
   ├─→ Generate Reminder Message
   │     ├─→ Doctor Name
   │     ├─→ Date & Time
   │     ├─→ Location
   │     └─→ Preparation Instructions
   ↓
   ├─→ Send Email Notification
   ├─→ Send SMS (if enabled)
   └─→ Create In-App Notification
   ↓
Log Reminder Sent
   ↓
[END] Reminders Sent

---

[CRON JOB] Hourly
   ↓
Query Appointments (start_time in next 2 hours AND status = 'confirmed')
   ↓
Send "Appointment Starting Soon" Alert
   ↓
[END]
```

**Schedule:**
- 24 hours before: Email + SMS
- 2 hours before: Push notification
- 30 minutes before: Final reminder

---

### Workflow 5: Payment Processing

```
[START] Appointment Marked "completed"
   ↓
[SYSTEM] Auto-generate Invoice
   ├─→ Consultation Fee
   ├─→ Additional Services
   ├─→ Tax Calculation
   └─→ Total Amount
   ↓
Send Invoice to Patient (Email + In-App)
   ↓
Patient Selects Payment Method
   ├─→ Cash (Pay at Reception)
   ├─→ Insurance (Submit Claim)
   ├─→ Card (Online Payment)
   └─→ Bank Transfer
   ↓
[IF Online Payment]
   ↓
   └─→ Redirect to Payment Gateway
       ↓
       ├─→ [IF Success] → Record Transaction
       │                → Update Status to "completed"
       │                → Generate Receipt
       │                → Send Confirmation Email
       ↓
       └─→ [IF Failed] → Log Error
                        → Notify Patient
                        → Allow Retry
   ↓
[IF Cash/Insurance]
   ↓
   └─→ Admin/Receptionist Marks as Paid
       ↓
       Generate Receipt
       ↓
       Update Payment Status
   ↓
[END] Payment Complete
```

**Payment Terms:**
- Online: Immediate processing
- Insurance: 7-30 days claim processing
- Cash: Same day

---

### Workflow 6: Admin User Management

```
[START] Admin Accesses User Management
   ↓
Select Action:
   ├─→ [CREATE USER]
   │     ↓
   │   Enter User Details
   │     ↓
   │   Assign Role (Admin/Doctor/Patient)
   │     ↓
   │   [IF Doctor] → Add Specialization, License, Schedule
   │     ↓
   │   [SYSTEM] Validate Data
   │     ↓
   │   Send Welcome Email with Credentials
   │     ↓
   │   [END]
   │
   ├─→ [EDIT USER]
   │     ↓
   │   Search/Select User
   │     ↓
   │   Modify Information
   │     ↓
   │   [SYSTEM] Validate Changes
   │     ↓
   │   Save & Notify User
   │     ↓
   │   [END]
   │
   └─→ [DEACTIVATE USER]
         ↓
       Search/Select User
         ↓
       [SYSTEM] Check for Active Appointments
         ↓
         ├─→ [IF Appointments Exist] → Show Warning
         │                            → Require Reassignment/Cancellation
         ↓
         └─→ [IF No Conflicts] → Mark User as Inactive
                                → Cancel Future Appointments
                                → Notify User
         ↓
       [END]
```

---

### Workflow 7: Review Moderation

```
[TRIGGER] Patient Submits Review
   ↓
[SYSTEM] Create Review (status: pending)
   ↓
Notify Admin of New Review
   ↓
[WAIT] Admin Reviews Content
   ↓
Admin Decision:
   ├─→ [APPROVE]
   │     ↓
   │   Update status to "approved"
   │     ↓
   │   Publish Review on Doctor Profile
   │     ↓
   │   Recalculate Doctor's Average Rating
   │     ↓
   │   [OPTIONAL] Notify Doctor
   │     ↓
   │   [END]
   │
   └─→ [REJECT]
         ↓
       Update status to "rejected"
         ↓
       [OPTIONAL] Send Reason to Patient
         ↓
       [END]
```

**Moderation Criteria:**
- No offensive language
- Relevant to medical service
- No personal information disclosure
- Legitimate patient (verified appointment)

**Timeline:**
- Review submission: Immediate
- Moderation: 1-2 business days

---

### Workflow 8: Emergency Appointment Handling

```
[START] Patient Calls/Visits with Emergency
   ↓
Receptionist/Admin Assesses Urgency
   ↓
Check Available Doctors (specialization match)
   ↓
[IF Doctor Available NOW]
   ↓
   └─→ Create Immediate Appointment
       ↓
       Override Schedule (if needed)
       ↓
       Notify Doctor
       ↓
       Check-in Patient
       ↓
       Proceed to Consultation Workflow
       ↓
       [END]
   ↓
[IF No Immediate Availability]
   ↓
   └─→ Check Next Available Slot (within 2 hours)
       ↓
       ├─→ [IF Available] → Book Priority Appointment
       │                   → Notify Patient & Doctor
       ↓
       └─→ [IF Not Available] → Refer to Emergency Room
                                → Log Referral
                                → Follow-up Call Next Day
       ↓
       [END]
```

---

## Database Indexes for Performance

```sql
-- Appointments
CREATE INDEX idx_appointments_doctor_date ON appointments(doctor_id, appointment_date, status);
CREATE INDEX idx_appointments_patient_date ON appointments(patient_id, appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);

-- Doctor Schedules
CREATE INDEX idx_schedule_doctor_day ON doctor_schedules(doctor_id, day_of_week, is_available);

-- Medical Records
CREATE INDEX idx_medical_patient ON medical_records(patient_id, created_at);
CREATE INDEX idx_medical_doctor ON medical_records(doctor_id, created_at);

-- Payments
CREATE INDEX idx_payment_status ON payments(payment_status);
CREATE INDEX idx_payment_patient ON payments(patient_id, paid_at);

-- Notifications
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read, created_at);

-- Reviews
CREATE INDEX idx_reviews_doctor ON reviews(doctor_id, is_approved);
```

---

## Business Rules Summary

1. **Appointment Booking Rules:**
   - Patients can book up to 30 days in advance
   - Minimum 2 hours notice for booking
   - Maximum 3 active appointments per patient
   - No duplicate appointments (same doctor, same day)

2. **Cancellation Rules:**
   - Free cancellation: 24+ hours before appointment
   - Late cancellation (< 24 hours): 50% fee
   - No-show: Full fee charged

3. **Doctor Availability Rules:**
   - Minimum 30-minute slots
   - Maximum 12 hours per day
   - At least 1 day off per week
   - Break time between sessions (30 minutes per 4 hours)

4. **Payment Rules:**
   - Payment due within 7 days of appointment
   - Late payment: Account restrictions
   - Refunds processed within 14 business days

5. **Review Rules:**
   - Only patients who completed appointments can review
   - One review per appointment
   - Review window: 30 days after appointment
   - Minimum 10 characters in comment

---

## Audit & Compliance

### Audit Logging
All critical operations are logged:
- User authentication attempts
- Medical record access
- Appointment status changes
- Payment transactions
- Schedule modifications
- Data exports

### GDPR Compliance
- Right to access: Patients can download all their data
- Right to erasure: Data deletion with 30-day retention
- Data portability: Export in JSON/PDF format
- Consent management: Explicit opt-in for marketing

### HIPAA Considerations (if applicable)
- Encrypted storage of medical records
- Secure transmission (HTTPS/TLS)
- Access controls and authentication
- Audit trails for all PHI access

---

## System Constraints

1. **Technical Constraints:**
   - Concurrent user capacity: 1000 simultaneous users
   - Database size: Scalable to millions of records
   - Response time: < 2 seconds for page load
   - Uptime: 99.9% availability

2. **Data Constraints:**
   - Max appointment duration: 4 hours
   - Max file upload size: 10MB per attachment
   - Medical record retention: 7 years minimum
   - User inactive period: 2 years before archival

3. **Integration Constraints:**
   - Email service: SMTP/API integration required
   - SMS gateway: Third-party service (Twilio, etc.)
   - Payment gateway: PCI-DSS compliant
   - Calendar sync: Google Calendar/Outlook optional

---

*Last Updated: 2025-10-06*
*Version: 1.0*
