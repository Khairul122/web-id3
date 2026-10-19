# SIMINAT

**English** | [Bahasa Indonesia](README.id.md)

**University Study Field Classification Using the ID3 Algorithm**

SIMINAT helps school guidance counselors interpret students' study field tendencies using report card grades, academic tracks, and school of origin. It displays classification results alongside their decision paths so counselors can discuss the reasoning with students.

The project was developed for Nadia Umaira's undergraduate thesis in Informatics at Universitas Malikussaleh, using student data from MAN 4 Aceh Utara (Matangkuli) and MAS Al-Muslimun Lhoksukon.

The system recommends a **broad study field**, labeled Saintek, Soshum, or Campuran depending on the model scenario. These recommendations support discussions with guidance counselors and parents. The application's interface is in Indonesian; this README preserves interface labels and dataset values where needed.

## Features

| Module | Function |
|---|---|
| Public website | Introduction to SIMINAT, study field exploration, usage workflow, ID3 explanation, and consultation guidance. |
| Authentication | Login, logout, account settings, and password changes. |
| Dashboard | Student data summary, active model, evaluation results, and recent predictions. |
| Student records | Search, filters, create, edit, delete, grade details, and Excel import with a preview. |
| Reference data | Management of schools, ID3 attributes, attribute domains, and grade discretization thresholds. |
| Model training | ID3 training, entropy and information gain calculations, decision trees, IF-THEN rules, and model activation. |
| Model evaluation | Stratified k-fold cross-validation, confusion matrices, accuracy, precision, recall, and F1. |
| Predictions | Recommendations from grade inputs, decision paths, example degree programs, and batch predictions for unlabeled students. |
| Reports | Printable student, model, evaluation, and prediction reports; CSV export where supported by the report type. |
| User management | Administrator management of admin and guidance counselor accounts. |

Administrators manage data, training, evaluation, and user accounts. Guidance counselors can view records and results, request recommendations through the input form, and use reports according to their permissions.

## Technology and requirements

- **PHP 8.1 or later**, with the `PDO`, `pdo_mysql`, `mbstring`, `zip`, `SimpleXML`, and `fileinfo` extensions.
- **MySQL 8 or MariaDB** for data storage.
- **Apache** with `mod_rewrite` and `.htaccess` support.
- **Native CSS and JavaScript ES6** for the interface.
- **Python 3** for independent verification and research data processing, when needed.

The application uses MVC with a service layer. Website installation does not require Composer or a frontend build step.

## Local installation

The examples below use Laragon on Windows. Run commands from the project root, where `index.php` is located.

### 1. Set up the directory and server

Place the project in Laragon's document root, for example:

```text
C:\laragon\www\web-id3
```

For XAMPP, use the `htdocs` directory. Start Apache and MySQL/MariaDB, then make sure the `php` command is available in your terminal.

```powershell
php --version
php -m
```

### 2. Configure the database connection

For a fresh installation, copy the example configuration:

```powershell
Copy-Item config/config.example.php config/config.php
```

If `config/config.php` already exists, update that file instead of overwriting it with the example. It returns the following configuration array:

```php
<?php
declare(strict_types=1);

return [
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'siminat',
    'db_user' => 'root',
    'db_password' => '',
];
```

The database name and credentials must match your local server. Git ignores the local configuration file. If you keep the example configuration logic, you can also supply values through the `SIMINAT_DB_HOST`, `SIMINAT_DB_PORT`, `SIMINAT_DB_NAME`, `SIMINAT_DB_USER`, and `SIMINAT_DB_PASSWORD` environment variables.

### 3. Initialize the database and default account

```powershell
php scripts/setup_auth.php
```

The script creates the database if it does not exist, prepares the tables, applies the password-change policy migration, and seeds the initial account and reference data. It does not overwrite existing accounts or reference data. The database user needs database creation privileges if the database has not been created yet.

SQL files for inspection or manual installation are available in `database/schema.sql`, `database/seed.sql`, `database/seed_master.sql`, and `database/migrations/`.

### 4. Open the application

| Page | Example URL |
|---|---|
| Home | `http://localhost/web-id3/` |
| Login | `http://localhost/web-id3/login.php` |
| Dashboard | `http://localhost/web-id3/admin/` |

If Laragon's virtual host is enabled, use `http://web-id3.test/`. Adjust the URL to match your directory or virtual host name.

Default seeded account:

| Username | Password | Role |
|---|---|---|
| `admin` | `admin123` | Administrator |

Change the default password through the account menu before making the application available to other users.

## Preparing data and models

### Import the dataset

The main dataset is [data/Dataset_Bersih_ID3.xlsx](data/Dataset_Bersih_ID3.xlsx). The importer reads the `Data_Numerik` and `Data_Tanpa_Label` worksheets. The expected research dataset contains 247 students: 147 labeled and 100 unlabeled.

In the dashboard, open **Data Siswa → Import**, select the server dataset or upload an `.xlsx` workbook, then review the data before saving it.

You can also import through the CLI:

```powershell
php scripts/import_dataset.php
```

To use another file with the same structure:

```powershell
php scripts/import_dataset.php "D:/dataset/Dataset_Bersih_ID3.xlsx"
```

**Import replaces all student records.** Use this step when you intend to reload the dataset, and back up databases that contain important work.

### Train and activate a model

Once the data is available:

1. Open **Training Model**, select the attributes and data scenario, then run training.
2. Review the calculation steps, decision tree, and generated rules.
3. Activate the model you want to use for recommendations.
4. Open **Pengujian Model** to evaluate it with k-fold cross-validation.
5. Use **Prediksi** for individual inputs or batch predictions.

Available scenarios:

| Code | Training data |
|---|---|
| `semua` | All labeled students. |
| `man` | Labeled students from MAN 4 Aceh Utara. |
| `mas` | Labeled students from MAS Al-Muslimun Lhoksukon. |
| `tanpa_campuran` | Students labeled Saintek or Soshum. |

To run the research workflow through the CLI:

```powershell
php scripts/proses_penelitian.php tanpa_campuran 5
```

This command **saves models and evaluations for all scenarios**, activates the `tanpa_campuran` model, and runs batch predictions. The first argument selects the active scenario; the second selects the number of folds, from 3 to 10. Defaults are `tanpa_campuran` and `5`.

## ID3 method

The system calculates entropy and information gain to select the splitting attribute at each node. Attribute order breaks ties to keep results consistent. Empty branches use the parent node's majority class; inputs with unknown attribute values use the majority class of the last node reached.

The nine initial attributes cover school of origin, academic track, grade categories for Religion, Arabic, Languages, Mathematics, Natural Sciences (MIPA), Social Sciences (IPS), and MIPA/IPS tendency. Discretization thresholds are read from the database, with these initial values:

| Stored category | Meaning and grade range |
|---|---|
| Tinggi | High: ≥ 95. |
| Sedang | Medium: ≥ 92 and < 95. |
| Rendah | Low: < 92. |
| Tidak Ada | The subject grade is unavailable. |

K-fold evaluation uses stratification and a fixed seed of 42. Prediction confidence comes from the class distribution at the decision tree's leaf; it differs from model accuracy on test data. See the [method verification report](docs/verifikasi-metode.md) for calculations, research results, and interpretation limits.

## Application structure

| Location | Contents |
|---|---|
| `index.php` | Public website. |
| `login.php`, `logout.php` | Authentication entry points. |
| `admin/` | Dashboard front controller and URL rewrite rules. |
| `app/core/` | Bootstrap, router, PDO connection, authentication, CSRF, and helpers. |
| `app/controllers/` | Request handling and page workflows. |
| `app/models/` | Data access. |
| `app/services/` | ID3, import, training, evaluation, and prediction logic. |
| `views/` | Layouts, pages, and view components. |
| `assets/` | Stylesheets, JavaScript, logo, WebP images, and local fonts. |
| `config/` | Database configuration. |
| `database/` | SQL schema, seeds, and migrations. |
| `data/` | Import-ready dataset, original sources in `sumber/`, and proposal in `referensi/`. |
| `scripts/` | PHP setup, import, and research workflow commands; Python source processing in `penelitian/`. |
| `tests/` | Algorithm, HTTP, and browser tests. |
| `storage/` | Uploads and application logs. |
| `output/` | Local verification output ignored by Git. |
| `docs/` | Documentation and development archives. |

## Testing

Run commands from the project root:

| Command | Coverage and requirements |
|---|---|
| `php tests/id3_manual_test.php` | Entropy, gain, decision trees, edge cases, and k-fold; no database required. |
| `python tests/verify_id3_dataset.py` | Recalculates ID3 from the workbook and compares it with the system; requires PHP, a populated database, and the dataset. |
| `python tests/auth_http_test.py` | HTTP authentication workflows using a temporary fixture account. |
| `python tests/admin_http_test.py` | Dashboard pages, CRUD, import, training, evaluation, predictions, reports, and permissions. |
| `python tests/auth_browser_test.py` | Browser-based login and account workflows. |
| `python tests/landing_sections_test.py` | Landing page navigation, interactive components, assets, and responsive layouts. |
| `python tests/landing_motion_test.py` | Animations, diagram replay, mobile menu, and reduced motion. |

HTTP and browser tests use `http://localhost/web-id3/`, so Apache, the database, and that directory name must match the test setup. Test fixtures create temporary data and clean it up afterward. Browser verification output is saved in `output/qa/`.

To set up browser testing:

```powershell
python -m pip install playwright
python -m playwright install chromium
```

Rebuilding the dataset from research sources requires `openpyxl` and Poppler's `pdftotext` command. Run `python scripts/penelitian/build_dataset.py` from the project root. Its supporting scripts are in the same directory, and original documents are grouped under `data/sumber/`. This command replaces `data/Dataset_Bersih_ID3.xlsx`; use it only when rebuilding the workbook from raw data. See the [source map](data/README.md).

## Troubleshooting

| Problem | What to check |
|---|---|
| Database connection fails | Check the MySQL/MariaDB service and values in `config/config.php`. |
| Tables are missing | Run database initialization and review migrations appropriate for an existing database. |
| Dashboard URLs return 404 | Enable `mod_rewrite` and make sure Apache allows `.htaccess` overrides. |
| `ZipArchive` or XML functions are unavailable | Enable `zip` and `SimpleXML` in the PHP configurations used by both the server and CLI. |
| Server dataset cannot be found | Make sure the file is located at `data/Dataset_Bersih_ID3.xlsx`. |
| Recommendations cannot run yet | Import data, train a model, and activate one model. |
| CLI PHP differs from Apache PHP | Check `php --ini` and the PHP version selected in Laragon. |

Application error details are stored in `storage/logs/app.log`. The PHP process needs write access to `storage/logs/` and `storage/uploads/`.

## Data and documentation

Research data contains student identities. Keep the `.htaccess` rules that restrict HTTP access to internal directories, and use the data only for authorized purposes. Apply equivalent access restrictions when using a server other than Apache.

Supporting documentation is currently in Indonesian:

- [Method verification and research results](docs/verifikasi-metode.md).
- [Initial project plan](docs/plan.md).
- [Latest landing page design](docs/landing-redesign.md).
- [Project structure and cleanup](docs/struktur-proyek.md).
- [Development guide](docs/agents.md).
- [Changelog](CHANGELOG.md).
