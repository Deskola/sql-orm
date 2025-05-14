# Simple ORM for Learning SQL

## Create MIgrations

```php
use DeskolaOrm\Connections\Database;
use DeskolaOrm\Migrations\Constraints;
use DeskolaOrm\Migrations\Migration;

try {
    $hospital_db = Database::getInstance('localhost', 'hospitaldb', 'root', '');

    // doctor migration table
    $doctor = new Migration(clone $hospital_db, 'doctors');
    $doctor->addColumn('DoctorID', 'int', 11, [Constraints::PK]);
    $doctor->addColumn('FirstName', 'string', 50);
    $doctor->addColumn('LastName', 'string', 50);
    $doctor->addColumn('Specialty', 'string', 50);
    $doctor->create();

    // patient migration table
    $patient = new Migration(clone $hospital_db, 'patients');
    $patient->addColumn('PatientID', 'int', 11, [Constraints::PK]);
    $patient->addColumn('FirstName', 'string', 50);
    $patient->addColumn('LastName', 'string', 50);
    $patient->addColumn(column: 'DateOfBirth', type: 'date');
    $patient->addColumn('VisitDate', 'date');
    $patient->addColumn('Diagnosis', 'string', 100);
    $patient->create();

    // appointment migration table
    $appointment = new Migration(clone $hospital_db, 'appointments');
    $appointment->addColumn('AppointmentID', 'int', 11, [Constraints::PK]);
    $appointment->addColumn('PatientID', 'int', 11, [Constraints::FK], ['patients' => 'PatientID']);
    $appointment->addColumn('DoctorID', 'int', 11, [Constraints::FK], ['patients' => 'PatientID']);
    $appointment->addColumn(column: 'AppointmentDate', type: 'date');
    $appointment->addColumn('Notes', 'string', '255');
    $appointment->create();


    // doctor migration table
} catch (RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}
```
