<?php

$login_path = 'd:\\Project_Will To Reles\\army_from_register\\resources\\views\\staff\\login.blade.php';
$login_content = file_get_contents($login_path);

$login_replacements = [
    'Staff Portal' => 'ផតថលបុគ្គលិក',
    'Mobile Staff Login' => 'ចូលគណនីបុគ្គលិក',
    'Use your Latin-name username and staff ID password to open your profile and upload documents.' => 'ប្រើប្រាស់ឈ្មោះអ្នកប្រើប្រាស់ និងលេខសម្ងាត់របស់អ្នកដើម្បីបើកប្រវត្តិរូបអ្នក និងបញ្ចូលឯកសារផ្សេងៗ។',
    'First login' => 'ការចូលប្រើលើកដំបូង',
    'Initial password = Staff ID. After login, the system will force a password change.' => 'លេខសម្ងាត់ដើម = លេខសម្គាល់បុគ្គលិក។ បន្ទាប់ពីការចូលប្រើប្រាស់ ប្រព័ន្ធនឹងតម្រូវឱ្យមានការផ្លាស់ប្តូរលេខសម្ងាត់។',
    'Username' => 'ឈ្មោះគណនី',
    'Password' => 'លេខសម្ងាត់',
    'Enter staff ID password' => 'បញ្ចូលលេខសម្ងាត់',
    'Login to Profile' => 'ចូលគណនី',
    'Need help?' => 'ត្រូវការជំនួយមែនទេ?',
    'If your username or staff ID is wrong, contact the admin team to verify your staff record.' => 'ប្រសិនបើឈ្មោះប្រើប្រាស់ ឬលេខសម្គាល់បុគ្គលិករបស់អ្នកមិនត្រឹមត្រូវ សូមទាក់ទងមកក្រុមរដ្ឋបាល ដើម្បីផ្ទៀងផ្ទាត់។',
    'Back to Main Portal' => 'ត្រឡប់ទៅទំព័រដើមវិញ',
    '>Show<' => '>បង្ហាញ<',
    "'Show'" => "'បង្ហាញ'",
    "'Hide'" => "'លាក់'",
];

foreach ($login_replacements as $search => $replace) {
    if ($search === '>Show<') {
        $login_content = str_replace(">
                                    Show
                                <", ">
                                    បង្ហាញ
                                <", $login_content);
    } else {
        $login_content = str_replace($search, $replace, $login_content);
    }
}
file_put_contents($login_path, $login_content);

$profile_path = 'd:\\Project_Will To Reles\\army_from_register\\resources\\views\\staff\\profile.blade.php';
$profile_content = file_get_contents($profile_path);

$profile_replacements = [
    'Khmer Name' => 'ឈ្មោះខ្មែរ',
    'English Name' => 'ឈ្មោះឡាតាំង',
    'Staff ID' => 'លេខសម្គាល់',
    'Gender' => 'ភេទ',
    'Position' => 'មុខតំណែង',
    'Rank' => 'ឋានន្តរស័ក្តិ',
    'Role' => 'តួនាទី',
    'Phone Number' => 'លេខទូរស័ព្ទ',
    'Staff Profile' => 'ប្រវត្តិរូបបុគ្គលិក',
    'Logout' => 'ចាកចេញ',
    '>Profile<' => '>ប្រវត្តិរូប<',
    'Account Status' => 'ស្ថានភាពគណនី',
    "'Active'" => "'សកម្ម'",
    "'Inactive'" => "'អសកម្ម'",
    'Documents' => 'ឯកសារ',
    'Profile incomplete' => 'ប្រវត្តិរូបមិនទាន់ពេញលេញ',
    'Some staff information is still missing. Contact the admin team if any profile detail needs correction.' => 'ព័ត៌មានបុគ្គលិកមួយចំនួននៅតែបាត់។ សូមទាក់ទងមកក្រុមរដ្ឋបាល ប្រសិនបើមានព័ត៌មានណាមួយត្រូវការកែតម្រូវ។',
    'Personal Info' => 'ព័ត៌មានផ្ទាល់ខ្លួន',
    'Your Details' => 'ព័ត៌មានលម្អិតរបស់អ្នក',
    'Change Password' => 'ផ្លាស់ប្តូរលេខសម្ងាត់',
    'Secure Documents' => 'ឯកសារសុវត្ថិភាព',
    'Document Upload' => 'ការបញ្ចូលឯកសារ',
    'This list is managed by admin at `/admin?section=staff-team-documents`. Staff upload files into those exact document types here.' => 'បញ្ជីត្រូវបានគ្រប់គ្រងដោយរដ្ឋបាល។ បុគ្គលិកត្រូវបញ្ចូលឯកសារតាមប្រភេទច្បាស់លាស់នៅទីនេះ។',
    'Admin has not created a document type list yet. You can still upload a private file with a custom title.' => 'រដ្ឋបាលមិនទាន់បានបង្កើតប្រភេទបញ្ជីឯកសារនៅឡើយទេ។ អ្នកនៅតែអាចបញ្ចូលឯកសារឯកជនបាន ដោយការដាក់ចំណងជើងជាក់លាក់។',
    'Upload Document' => 'បញ្ចូលឯកសារ',
    " file(s) uploaded'" => " ឯកសារបានបញ្ចូល'",
    "'No file uploaded yet'" => "'មិនទាន់មានឯកសារទេ'",
    "'Uploaded'" => "'បានបញ្ជូល'",
    "'Missing'" => "'បាត់ឯកសារ'",
    "'Uploaded by you'" => "'បញ្ចូលដោយអ្នក'",
    "'Uploaded by admin'" => "'បញ្ចូលដោយរដ្ឋបាល'",
    "'Pending'" => "'រង់ចាំការអនុម័ត'",
    "'Approved'" => "'បានអនុម័តរួចរាល់'",
    ">Download<" => ">ទាញយក<",
    ">Delete<" => ">លុប<",
    "'Upload more'" => "'បញ្ចូលបន្ថែម'",
    "'Upload'" => "'បញ្ចូល'",
    ">Upload<" => ">បញ្ចូល<",
    'No document list from admin yet' => 'មិនទាន់មានបញ្ជីឯកសារពីរដ្ឋបាលនៅឡើយទេ',
    'Uploads will be stored as private legacy documents until the admin creates managed document types.' => 'ឯកសាររាល់ការបញ្ចូលនីមួយៗ នឹងត្រូវរក្សាទុកជាឯកសារឯកជន រហូតដល់រដ្ឋបាលបង្កើតបញ្ជី។',
    'Other Documents' => 'ឯកសារផ្សេងៗទៀត',
    "'Document'" => "'ឯកសារ'",
    "'Admin File'" => "'ឯកសាររដ្ឋបាល'",
    "'Provided by admin'" => "'ផ្តល់ដោយរដ្ឋបាល'",
    'Add Documents' => 'បន្ថែមឯកសារ',
    'Upload File' => 'ជ្រើសរើសឯកសារបញ្ជូល',
    'Choose one document type from the admin-managed list.' => 'ជ្រើសរើសប្រភេទឯកសារណាមួយពីបញ្ជីរដ្ឋបាល។',
    'Add a clear title so the admin can identify this legacy document upload.' => 'បញ្ជូលចំណងជើងច្បាស់លាស់ដើម្បីឲ្យរដ្ឋបាលងាយស្រួលកំណត់អត្តសញ្ញាណ។',
    'Document Type' => 'ប្រភេទឯកសារ',
    'Select required document' => 'ជ្រើសរើសឯកសារដែលត្រូវការ',
    'Document Title' => 'ចំណងជើងឯកសារ',
    'Example: Service Letter' => 'ឧទាហរណ៍៖ លិខិតបញ្ជាក់សេវាកម្ម',
    'Attach Document' => 'ភ្ជាប់ឯកសារ',
    'Tap to browse file' => 'ចុចដើម្បីរើសឯកសារបញ្ជូល',
    'Drag and drop also works on supported devices.' => 'អ្នកក៏អាចអូសឯកសារទម្លាក់នៅទីនេះបានផងដែរ។',
    'No file selected' => 'មិនទាន់មានឯកសារ',
    '>Rules<' => '>ច្បាប់លក្ខខណ្ឌ<',
    'Accepted: PDF, JPG, PNG, DOCX. Max size 10MB. Only you and the admin team can access the file.' => 'អនុញ្ញាត៖ PDF, JPG, PNG, DOCX (អតិបរមា 10MB)។ មានតែអ្នក និងរដ្ឋបាលប៉ុណ្ណោះដែលអាចមើលឯកសារនេះបាន។',
    '>Cancel<' => '>បោះបង់<',
    'Uploading document...' => 'កំពុងបញ្ចូលឯកសារ...',
    'Please keep this page open until the upload finishes.' => 'សូមកុំបិទទំព័រនេះរហូតដល់ការបញ្ចូលត្រូវបានបញ្ចប់។',
];

foreach ($profile_replacements as $search => $replace) {
    if ($search === '>Profile<') {
        $profile_content = str_replace('>Profile<', '>ប្រវត្តិរូប<', $profile_content);
    } elseif ($search === '>Download<') {
        $profile_content = str_replace(">
                                                            Download
                                                        <", ">
                                                            ទាញយក
                                                        <", $profile_content);
        $profile_content = str_replace(">
                                                Download
                                            <", ">
                                                ទាញយក
                                            <", $profile_content);
    } elseif ($search === '>Delete<') {
        $profile_content = str_replace(">
                                                                    Delete
                                                                <", ">
                                                                    លុប
                                                                <", $profile_content);
        $profile_content = str_replace(">
                                                    Delete
                                                <", ">
                                                    លុប
                                                <", $profile_content);
    } elseif ($search === '>Upload<') {
            $profile_content = str_replace(">
                        Upload
                    <", ">
                        បញ្ចូលឯកសារ
                    <", $profile_content);
    } elseif ($search === '>Cancel<') {
            $profile_content = str_replace(">
                        Cancel
                    <", ">
                        បោះបង់
                    <", $profile_content);
    } elseif ($search === '>Rules<') {
        $profile_content = str_replace('Rules</p>', 'ច្បាប់លក្ខខណ្ឌ</p>', $profile_content);
    } else {
        $profile_content = str_replace($search, $replace, $profile_content);
    }
}
file_put_contents($profile_path, $profile_content);
