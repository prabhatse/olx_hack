<?php  
/*
 * Copyright 2014 Empodex PHP Framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */










/* We can use the below function to define the constants 
 * in system, we will pass the array as params.
 */
function sternDefine($config) {

	foreach ($config as $key => $value) { 
		define($key,$value);
		# code...
	}
}




define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('SUCCESS',true);
define('FAILURE',false);

/* User types defined in system */
define('SUPER_ADMIN','super_admin');
define('OP_ADMIN','op_admin');
define('DB_ADMIN','db_admin');

define('DATA_ENTRY','data_entry');
define('OP_MANAGER','op_manager');
define('SALES_MANAGER','sales_manager');
define('SALES','sales');
define('ACCOUNTS_MANAGER','accounts_manager');
define('VENDOR_MANAGER','vendor_manager');
define('EXTERNAL_VENDOR','external_vendor');
define('INTERNAL_VENDOR','internal_vendor');
define('CAM','cam');
define('SR_VER_ANALYST','sr_ver_analyst');
define('VER_ANALYST','ver_analyst');
define('REPORT_MANAGER','report_manager');
define('SR_REPORT_WRITER','sr_report_writer');
define('REPORT_WRITER','report_writer');
define('QUALITY_MANAGER','quality_manager');
define('CLIENT','client');
define('CANDIDATE','candidate');


//Constants for case status 

define('INVOICE_PENDING', 'invoice_pending');


//Constants for payment status 
define('UNBILLED', 'unbilled');
define('UNPAID', 'unpaid');
define('PAID', 'paid');

define('VIA_FORM', 'via_form');
define('VIA_DOCUMENT', 'via_document');

define('NEW_CASE', 'new_case');

//Constants for Operations Status
define('NOT_UPDATED', 'Not_updated');
define('INITIATED','initiated');
define('FOR_DATA_ENTRY', 'For_data_entry');
define('OPEN', 'Open');
define('LOCKED', 'Locked');
define('CLOSED', 'Closed');
define('COMPLETED', 'completed');
define('PENDING_REVIEW', 'pending_review');
define('REVIEW_ISSUE', 'review_issue');
define('WORK_IN_PROGRESS', 'work_in_progress');
define('INSUFFICIENT_AIR', 'insufficient_air');
define('VERIFIED', 'verified');
define('PARTIALLY_VERIFIED', 'partially_verified');
define('DISCREPANCY','discrepancy');
define('UNABLE_TO_VERIFY','unable_to_verify');
define('STOP_CASE','stop_case');
define('AWAITING','awaiting');

define('NOT_VERIFIED', 'not_verified');
define('REJECTED', 'rejected');

define('EXHIBIT', 'exhibit');
define('USER_SUBMITTED', 'user_submitted');
define('PERMANENT', 'Permanent');
define('CURRENT', 'Present');
define('PRESENT', 'Present');
define('PERSONAL', 'Personal');
define('PROFESSIONAL', 'Professional');

define('CONTACT_UNIVERSITY', 'contact_university');
define('UNIVERSITY_POC', 'university_poc');
define('SEND_DD_TO_UNIVERSITY', 'send_dd_to_university');
define('CHECK_ONLINE_RESULT', 'check_online_result');
define('CONTACT_VENDOR', 'contact_vender');

define('CONTACT_EMPLOYER', 'contact_employer');
define('EMPLOYER_POC', 'employer_poc');
define('SEND_COURIER', 'send_courier');

//Constants for service type
define('DOMESTIC', 'Domestic');
define('INTERNATIONAL', 'International');
define('BOTH', 'Both');

//Constats for education
define('GRAD', 'graduate');
define('POST_GRAD', 'post_graduate');
define('DIPLOMA', 'Diploma');
define('LAWYERS', 'Lawyers');


//Constants for the process
define('EDUCATION', 'education');
define('EMPLOYMENT', 'employment');
define('ADDRESS', 'address');
define('POLICE', 'police');
define('RATION_CARD', 'rationcard');
define('PAN', 'pan_no');
define('PASSPORT', 'passport');
define('VOTER_ID', 'voter_id');
define('GLOBALS', 'global');
define('OFAC', 'ofac');
define('GDB', 'gdb');
define('DRIVING_LICENCE', 'driving_license');
define('REFERENCE', 'reference');
define('COURT_RECORD', 'court_record');
define('DATABASE', 'database');
define('IDENTITY', 'identity');
define('DRUG_TEST', 'drug_test');

/* End of file constants.php */
/* Location: ./application/config/constants.php */