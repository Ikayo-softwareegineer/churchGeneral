# Church CMS - Dynamic Sermons & Testimonies System

A complete dynamic content management system for churches to upload, manage, and display sermons and testimonies.

## Features

### Dynamic Content Management
- ✅ **Database-driven content**: All sermons and testimonies are stored in MySQL database
- ✅ **Real-time statistics**: Live view counts, download tracking, and content analytics
- ✅ **Search functionality**: Search by title, author, or description
- ✅ **Category filtering**: Filter content by categories (faith, prayer, healing, etc.)
- ✅ **Pagination**: Efficient content loading with pagination
- ✅ **Media support**: Video, audio, PDF, and text file support

### Admin Features
- ✅ **Content upload**: Easy file upload with drag-and-drop support
- ✅ **Content management**: View, edit, and delete uploaded content
- ✅ **File validation**: Automatic file type and size validation
- ✅ **Secure file storage**: Files stored in protected uploads directory

### User Experience
- ✅ **Responsive design**: Works on all devices
- ✅ **Modern UI**: Beautiful gradient design with smooth animations
- ✅ **Media playback**: Built-in video and audio players
- ✅ **Download functionality**: Direct file downloads
- ✅ **Share functionality**: Social sharing capabilities

## Installation

### 1. Database Setup
```sql
-- Import the database structure
mysql -u root -p < database.sql
```

### 2. Configuration
Edit `config/database.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'church_cms');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. File Permissions
Ensure the uploads directory is writable:
```bash
chmod 755 uploads/
```

### 4. Web Server Configuration
Make sure your web server supports:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- File uploads (max 100MB)

## Usage

### For Administrators

#### Uploading Content
1. Navigate to `admin-upload-content.php`
2. Fill in the content details:
   - **Type**: Sermon or Testimony
   - **Title**: Content title
   - **Author**: Speaker or author name
   - **Description**: Content description
   - **Category**: Optional category (faith, prayer, healing, etc.)
   - **File**: Upload media file (video, audio, PDF, text)
3. Click "Upload Content"

#### Managing Content
- View recent uploads in the admin panel
- Click "View" to preview content
- Click "Edit" to modify content (feature coming soon)
- Click "Delete" to remove content (feature coming soon)

### For Users

#### Browsing Content
1. Visit `sermons-testimonies.php`
2. Switch between Sermons and Testimonies tabs
3. Use search bar to find specific content
4. Filter by categories using the filter buttons
5. Navigate through pages using pagination

#### Viewing Content
1. Click on any content card
2. Media will play directly in the browser
3. Download files using the download button
4. Share content using the share button

## File Structure

```
CMS-Web/
├── config/
│   └── database.php          # Database configuration and helper functions
├── uploads/
│   └── .htaccess            # File access protection
├── sermons-testimonies.php   # Main dynamic content page
├── view-content.php         # Individual content viewer
├── admin-upload-content.php # Admin upload interface
├── database.sql             # Database structure
└── README.md               # This file
```

## Database Schema

### Content Table
- `id`: Unique identifier
- `type`: 'sermon' or 'testimony'
- `title`: Content title
- `author`: Speaker/author name
- `description`: Content description
- `category`: Content category
- `file_path`: File storage path
- `file_type`: File type (video, audio, pdf, text)
- `file_size`: File size in bytes
- `upload_date`: Upload timestamp
- `views`: View count
- `downloads`: Download count
- `status`: 'active' or 'inactive'

## Supported File Types

### Video
- MP4, AVI, MOV

### Audio
- MP3, WAV

### Documents
- PDF, TXT, DOC, DOCX

## Security Features

- ✅ **SQL Injection Protection**: Prepared statements
- ✅ **XSS Protection**: HTML escaping
- ✅ **File Upload Security**: Type and size validation
- ✅ **Directory Protection**: .htaccess file protection
- ✅ **Input Validation**: Server-side validation

## Performance Features

- ✅ **Database Indexing**: Optimized queries
- ✅ **Pagination**: Efficient content loading
- ✅ **File Caching**: Browser caching for media files
- ✅ **Responsive Images**: Optimized for different screen sizes

## Customization

### Styling
Edit the CSS variables in the `<style>` section:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --accent-color: #f093fb;
    --danger-color: #f5576c;
}
```

### Categories
Add new categories by uploading content with new category names. They will automatically appear in the filter options.

### File Size Limits
Modify the upload limit in `admin-upload-content.php`:
```php
if ($fileSize > 100 * 1024 * 1024) { // 100MB limit
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running

2. **File Upload Fails**
   - Check file permissions on uploads directory
   - Verify PHP upload settings in php.ini
   - Check file size limits

3. **Media Not Playing**
   - Ensure proper MIME types are set
   - Check browser compatibility
   - Verify file format support

### Error Logs
Check your web server error logs for detailed error messages.

## Future Enhancements

- [ ] User authentication system
- [ ] Content editing interface
- [ ] Bulk upload functionality
- [ ] Advanced search filters
- [ ] Content scheduling
- [ ] Analytics dashboard
- [ ] Mobile app integration
- [ ] Social media integration

## Support

For technical support or feature requests, please contact the development team.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Note**: This system is now completely dynamic! All content uploaded through the admin interface will be automatically displayed on the main page with full search, filter, and pagination functionality. 