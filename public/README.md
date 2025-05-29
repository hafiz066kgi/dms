# POLYPARTS ISO 9001 Document Management System (DMS)

A secure and user-friendly Document Management System (DMS) designed for ISO 9001 environments, enabling controlled document handling, robust user access, and complete auditability.  
**Design by Mohammad Al-Hafiz**

---

## Features

- **Secure Login**
  - Username/password authentication
  - Role-based access: Superadmin, Admin, Viewer

- **Document Masterlist**
  - Tracks: No., Document No., Revision, Document Name, Retention Period, Department, Person in Charge (PIC)

- **Document Management**
  - Upload, view, download, and delete documents (role-based permissions)
  - Secure, organized `/uploads` folder

- **User Management**
  - Superadmin can create, edit, deactivate users and assign roles

- **Admin Dashboard**
  - Responsive Bootstrap 5 Admin UI  
  - Branded with company logo (Cooper Black font)

- **Audit Log**
  - All user/document actions tracked in SQL audit log

- **Powerful Search and Filtering**
  - Search by document name, number, PIC, and category
  - Filter by department or document type

- **Category Management**
  - Documents tagged for quick retrieval

---

## Setup

1. **Database**
   - Import `sql/create_tables.sql` into your MySQL server.

2. **Configuration**
   - Edit `config/config.php` with your database credentials.

3. **File Structure**
   - Place all PHP files according to the project’s folder structure.

4. **Web Server**
   - Set the document root to `/public` (e.g., `http://localhost/dms/public`).

5. **Uploads Folder**
   - Ensure `/uploads` and all subfolders are writable by the web server.

6. **Default Users**
   - Superadmin: `superadmin` / `Superadmin123`
   - Admin:     `admin` / `Admin123`
   - Viewer:    `viewer` / `Viewer123`

---

## Security & ISO Compliance

- Change all default passwords immediately upon installation.
- Enforce HTTPS for all web access (update server settings).
- Limit access to configuration and uploads folders using `.htaccess` or web server rules.
- Only Superadmin may manage users and view full audit logs.
- All document uploads are virus-scanned and restricted by file type/size.
- Audit log captures document creation, updates, deletions, logins, and permission changes.

---

## Customization & Branding

- Update `assets/logo.png` and set your brand color scheme in `css/custom.css`.
- Cooper Black font is used for company logo and primary headers.

---

## Additional Recommendations

- **Backup:** Schedule regular backups of your SQL database and `/uploads`.
- **Updates:** Monitor dependencies (PHP, Bootstrap) for security patches.
- **Documentation:** Maintain current user and admin manuals as controlled documents.

---

## Notes

- For **production**, always set secure user passwords and enable HTTPS.
- Review user permissions regularly for ongoing ISO compliance.
- For technical support, contact: `hafiz066kgi@gmail.com`