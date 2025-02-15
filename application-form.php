<?php
/*
Plugin Name: Application Form 
Description: A plugin to create a application form and store data in custom database table.
Version: 1.0
Author: Payal Sharma
*/
function wpmsf_enqueue_assets() {
    $plugin_url = plugin_dir_url(__FILE__);

    // Enqueue CSS file
    wp_enqueue_style('wpmsf-style',$plugin_url . 'assets/style.css',array(),'1.0','all');

    // Enqueue JS file
    wp_enqueue_script('wpmsf-script',$plugin_url . 'assets/script.js',array('jquery'),'1.0',true);

    // Localize script with AJAX URL
    wp_localize_script('wpmsf-script', 'wpmsf_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'wpmsf_enqueue_assets');
//Create Database Tables on Plugin Activation
function wpmsf_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $form_data_table = $wpdb->prefix . 'form_data';

    $sql = "CREATE TABLE IF NOT EXISTS $form_data_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id_users BIGINT(20) UNSIGNED,  -- Add user_id_users field
        position_applied VARCHAR(255),
        date_of_readiness DATE,
        surname VARCHAR(255),
        name VARCHAR(255),
        father_name VARCHAR(255),
        mother_name VARCHAR(255),
        date_of_birth DATE,
        nationality VARCHAR(255),
        place_of_birth VARCHAR(255),
        marital_status VARCHAR(255),
        children_under_18 INT,
        home_address TEXT,
        home_zip VARCHAR(10),
        contact_phone VARCHAR(50),
        email VARCHAR(255),
        password VARCHAR(255),
        skype_telegram VARCHAR(255),
        next_kin VARCHAR(255),
        relation VARCHAR(255),
        next_kin_address VARCHAR(255),
        next_kin_phone VARCHAR(50),
        height INT,
        weight INT,
        size_overall VARCHAR(255),
        eye_color VARCHAR(50),
        hair_color VARCHAR(50),
        shoes VARCHAR(20),
        maritime_college VARCHAR(255),
        department VARCHAR(255),
        education_from DATE,
        education_till DATE,
        date_signed DATE,
        name_sign VARCHAR(50),
        FOREIGN KEY (user_id_users) REFERENCES $wpdb->users(ID) ON DELETE CASCADE  -- Add foreign key constraint
    ) $charset_collate;";
    // Secondary table
    $application_data_table = $wpdb->prefix . 'application_data';
    $application_data_sql = "CREATE TABLE IF NOT EXISTS $application_data_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        user_id_users BIGINT(20) UNSIGNED,  -- Add user_id_users field
        document_type VARCHAR(255) NOT NULL,
        document_number VARCHAR(255),
        issued_date DATE,
        valid_until DATE,
        place VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES $form_data_table(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id_users) REFERENCES $wpdb->users(ID) ON DELETE CASCADE  -- Add foreign key constraint
    ) $charset_collate;";
    //seaman's record table
    $seaman_records_table = $wpdb->prefix . 'seaman_records';
    $seaman_records_sql = "CREATE TABLE IF NOT EXISTS $seaman_records_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        user_id_users BIGINT(20) UNSIGNED,  -- Add user_id_users field
        flag VARCHAR(255),
        flag_number VARCHAR(255),
        issued_date DATE,
        valid_until DATE,
        place VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES $form_data_table(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id_users) REFERENCES $wpdb->users(ID) ON DELETE CASCADE  -- Add foreign key constraint
    ) $charset_collate;";
    //Sea service record table
    $previous_sea_service = $wpdb->prefix . 'sea_service';
    $sea_service_sql = "CREATE TABLE IF NOT EXISTS $previous_sea_service(
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        user_id_users BIGINT(20) UNSIGNED,  -- Add user_id_users field
        from_date DATE,
        to_date DATE,
        position VARCHAR(255),
        salary VARCHAR(10),
        name_vessel VARCHAR(255),
        ship_owner VARCHAR(255),
        type_vessel VARCHAR(255),
        type_engine VARCHAR(255),
        build_year VARCHAR(255),
        dwt VARCHAR(255),
        bhp VARCHAR(255),
        flag VARCHAR(255), 
        crewing_agent VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES $form_data_table(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id_users) REFERENCES $wpdb->users(ID) ON DELETE CASCADE  -- Add foreign key constraint
    ) $charset_collate;";
    //previous employers record table
    $previous_employers = $wpdb->prefix . 'employers_table';
    $previous_employers_sql = "CREATE TABLE IF NOT EXISTS $previous_employers(
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        user_id_users BIGINT(20) UNSIGNED,  -- Add user_id_users field
        company VARCHAR(255),
        person_in_charge VARCHAR(255),
        contact_details VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES $form_data_table(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id_users) REFERENCES $wpdb->users(ID) ON DELETE CASCADE  -- Add foreign key constraint
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($application_data_sql);
    dbDelta($seaman_records_sql);
    dbDelta($sea_service_sql);
    dbDelta($previous_employers_sql);
}
register_activation_hook(__FILE__, 'wpmsf_create_tables');

// Display Form Shortcode
function wpmsf_display_application_form() {
    // if (isset($_GET['success'])) {
    //     echo '<p class="success-message">Form submitted successfully!</p>';
    //     echo '
    //         <script>
    //             setTimeout(function() {
    //                 var message = document.querySelector(".success-message");
    //                 if (message) {
    //                     message.style.display = "none";
    //                      // Redirect to the login page after the message disappears
    //                   // window.location.href = "http://167.71.228.17/stellamarishipping/login/"; // Redirect to login page
    //                 }
    //             }, 3000); // 5 seconds
    //         </script>
    //     ';
    // }
    if (isset($_GET['success'])) {
        echo '<p class="success-message">Form submitted successfully!</p>';
        echo '
            <script>
                setTimeout(function() {
                    var message = document.querySelector(".success-message");
                    if (message) {
                        message.style.display = "none";
                    }
                }, 3000); 
            </script>
        ';
    }
    ob_start();
    ?>
    <form method="post" action="" id="applicationForm">
        <div class="form-container">
            <div class="form-header">
                <h2>Application Form</h2>
            </div>

            
            <div class="top_application form_outer">
            <table>
                <tr>
                    <!-- <th>Position applied for:</th>
                    <td><input type="text" name="position_applied" placeholder="Position applied for" required></td> -->
                    <th>Position applied for:  <span style="color: red;">*</span></th>
                    <td>
                    <select name="position_applied" class="required-field">
                        <option value="- SELECT -" selected disabled>- Select -</option>
                        <option value="Master">Master</option>
                        <option value="Chief Engineer">Chief Engineer</option>
                        <option value="Chief Officer">Chief Officer</option>
                        <option value="Second Officer">Second Officer</option>
                        <option value="Second Engineer">Second Engineer</option>
                        <option value="Third Engineer">Third Engineer</option>
                        <option value="Third Officer">Third Officer</option>
                        <option value="Electrical Engineer">Electrical Engineer</option>
                        <option value="Fourth Engineer">Fourth Engineer</option>
                        <option value="Pumpman">Pumpman</option>
                        <option value="Bosun">Bosun</option>
                        <option value="Able Seaman">Able Seaman</option>
                        <option value="Ordinary Seaman">Ordinary Seaman</option>
                        <option value="Deck Cadet">Deck Cadet</option>
                        <option value="Motorman">Motorman</option>
                        <option value="Motorman Turner">Motorman Turner</option>
                        <option value="Wiper">Wiper</option>
                        <option value="Fitter">Fitter</option>
                        <option value="Cook">Cook</option>
                        <option value="Messman">Messman</option>
                        <option value="Superintendent">Superintendent</option>
                        <option value="Junior Officer">Junior Officer</option>
                        <option value="Junior Engineer">Junior Engineer</option>
                        <option value="ETO Assistant">ETO Assistant</option>
                        <option value="Engine Cadet">Engine Cadet</option>
                        <option value="Oiler">Oiler</option>
                        <option value="Painter">Painter</option>
                        <option value="Electrical Cadet">Electrical Cadet</option>
                    </select>
                </td>

                    <th>Date of readiness:  <span style="color: red;">*</span></th>
                    <td><input type="date" name="date_of_readiness" class="required-field"></td>
                </tr>
                <tr>
                    <th>Surname:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="surname" placeholder="Surname" class="required-field"></td>
                    <th>Name:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="name" placeholder="Name" class="required-field"></td>
                </tr>
                <tr>
                    <th>Father's Name:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="father_name" placeholder="Father's Name" class="required-field"></td>
                    <th>Mother's Name:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="mother_name" placeholder="Mother's Name" class="required-field"></td>
                </tr>
                <tr>
                    <th>Date of Birth:  <span style="color: red;">*</span></th>
                    <td><input type="date" name="date_of_birth" placeholder="Date of Birth" class="required-field"></td>
                    <th>Nationality:  <span style="color: red;">*</span></th>
                    <td>
                        <select name="nationality" class="required-field">
                            <option value="" selected disabled>- Select -</option>
                            <option value="Afghanistan">Afghanistan</option>
                            <option value="Algeria">Algeria</option>
                            <option value="American Samoa">American Samoa</option>
                            <option value="Andorra">Andorra</option>
                            <option value="Angola">Angola</option>
                            <option value="Anguilla">Anguilla</option>
                            <option value="Antarctica">Antarctica</option>
                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                            <option value="Argentina">Argentina</option>
                            <option value="Armenia">Armenia</option>
                            <option value="Aruba">Aruba</option>
                            <option value="Australia">Australia</option>
                            <option value="Austria">Austria</option>
                            <option value="Azerbaijan">Azerbaijan</option>
                            <option value="Bahamas (The)">Bahamas (The)</option>
                            <option value="Bahrain">Bahrain</option>
                            <option value="Bangladesh">Bangladesh</option>
                            <option value="Barbados">Barbados</option>
                            <option value="Belarus">Belarus</option>
                            <option value="Belgium">Belgium</option>
                            <option value="Belize">Belize</option>
                            <option value="Benin">Benin</option>
                            <option value="Bermuda">Bermuda</option>
                            <option value="Bhutan">Bhutan</option>
                            <option value="Bolivia (Plurinational State of)">Bolivia (Plurinational State of)</option>
                            <option value="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                            <option value="Botswana">Botswana</option>
                            <option value="Bouvet Island">Bouvet Island</option>
                            <option value="Brazil">Brazil</option>
                            <option value="British Indian Ocean Territory (the)">British Indian Ocean Territory (the)</option>
                            <option value="Brunei Darussalam">Brunei Darussalam</option>
                            <option value="Bulgaria">Bulgaria</option>
                            <option value="Burkina Faso">Burkina Faso</option>
                            <option value="Burundi">Burundi</option>
                            <option value="Cabo Verde">Cabo Verde</option>
                            <option value="Cambodia">Cambodia</option>
                            <option value="Cameroon">Cameroon</option>
                            <option value="Canada">Canada</option>
                            <option value="Cayman Islands (the)">Cayman Islands (the)</option>
                            <option value="Central African Republic (the)">Central African Republic (the)</option>
                            <option value="Chad">Chad</option>
                            <option value="Chile">Chile</option>
                            <option value="China">China</option>
                            <option value="Christmas Island">Christmas Island</option>
                            <option value="Cocos (Keeling) Islands (the)">Cocos (Keeling) Islands (the)</option>
                            <option value="Colombia">Colombia</option>
                            <option value="Comoros (the)">Comoros (the)</option>
                            <option value="Congo (the Democratic Republic of the)">Congo (the Democratic Republic of the)</option>
                            <option value="Congo (the)">Congo (the)</option>
                            <option value="Cook Islands (the)">Cook Islands (the)</option>
                            <option value="Costa Rica">Costa Rica</option>
                            <option value="Cote d Ivoire">Cote d Ivoire</option>
                            <option value="Croatia">Croatia</option>
                            <option value="Cuba">Cuba</option>
                            <option value="Curaçao">Curaçao</option>
                            <option value="Cyprus">Cyprus</option>
                            <option value="Czechia">Czechia</option>
                            <option value="Denmark">Denmark</option>
                            <option value="Djibouti">Djibouti</option>
                            <option value="Dominica">Dominica</option>
                            <option value="Dominican Republic (the)">Dominican Republic (the)</option>
                            <option value="Ecuador">Ecuador</option>
                            <option value="Egypt">Egypt</option>
                            <option value="El Salvador">El Salvador</option>
                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                            <option value="Eritrea">Eritrea</option>
                            <option value="Estonia">Estonia</option>
                            <option value="Eswatini">Eswatini</option>
                            <option value="Ethiopia">Ethiopia</option>
                            <option value="European Union">European Union</option>
                            <option value="Falkland Islands (the) [Malvinas]">Falkland Islands (the) [Malvinas]</option>
                            <option value="Faroe Islands (the)">Faroe Islands (the)</option>
                            <option value="Fiji">Fiji</option>
                            <option value="Finland">Finland</option>
                            <option value="France">France</option>
                            <option value="French Guiana">French Guiana</option>
                            <option value="French Polynesia">French Polynesia</option>
                            <option value="French Southern Territories (the)">French Southern Territories (the)</option>
                            <option value="Gabon">Gabon</option>
                            <option value="Gambia (the)">Gambia (the)</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Germany">Germany</option>
                            <option value="Ghana">Ghana</option>
                            <option value="Gibraltar">Gibraltar</option>
                            <option value="Greece">Greece</option>
                            <option value="Greenland">Greenland</option>
                            <option value="Grenada">Grenada</option>
                            <option value="Guadeloupe">Guadeloupe</option>
                            <option value="Guam">Guam</option>
                            <option value="Guatemala">Guatemala</option>
                            <option value="Guernsey">Guernsey</option>
                            <option value="Guinea">Guinea</option>
                            <option value="Guinea-Bissau">Guinea-Bissau</option>
                            <option value="Guyana">Guyana</option>
                            <option value="Haiti">Haiti</option>
                            <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
                            <option value="Holy See (the)">Holy See (the)</option>
                            <option value="Honduras">Honduras</option>
                            <option value="Hong Kong">Hong Kong</option>
                            <option value="Hungary">Hungary</option>
                            <option value="Iceland">Iceland</option>
                            <option value="India">India</option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Iran (Islamic Republic of)">Iran (Islamic Republic of)</option>
                            <option value="Iraq">Iraq</option>
                            <option value="Ireland">Ireland</option>
                            <option value="Isle of Man">Isle of Man</option>
                            <option value="Israel">Israel</option>
                            <option value="Italy">Italy</option>
                            <option value="Jamaica">Jamaica</option>
                            <option value="Japan">Japan</option>
                            <option value="Jersey">Jersey</option>
                            <option value="Jordan">Jordan</option>
                            <option value="Kazakhstan">Kazakhstan</option>
                            <option value="Kenya">Kenya</option>
                            <option value="Kiribati">Kiribati</option>
                            <option value="Korea (the Democratic Peoples Republic of)">Korea (the Democratic Peoples Republic of)</option>
                            <option value="Korea (the Republic of)">Korea (the Republic of)</option>
                            <option value="Kuwait">Kuwait</option>
                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                            <option value="Lao Peoples Democratic Republic (the)">Lao Peoples Democratic Republic (the)</option>
                            <option value="Latvia">Latvia</option>
                            <option value="Lebanon">Lebanon</option>
                            <option value="Lesotho">Lesotho</option>
                            <option value="Liberia">Liberia</option>
                            <option value="Libya">Libya</option>
                            <option value="Liechtenstein">Liechtenstein</option>
                            <option value="Lithuania">Lithuania</option>
                            <option value="Luxembourg">Luxembourg</option>
                            <option value="Macao">Macao</option>
                            <option value="Madagascar">Madagascar</option>
                            <option value="Malawi">Malawi</option>
                            <option value="Malaysia">Malaysia</option>
                            <option value="Maldives">Maldives</option>
                            <option value="Mali">Mali</option>
                            <option value="Malta">Malta</option>
                            <option value="Marshall Islands (the)">Marshall Islands (the)</option>
                            <option value="Martial Island">Martial Island</option>
                            <option value="Martinique">Martinique</option>
                            <option value="Mauritania">Mauritania</option>
                            <option value="Mauritius">Mauritius</option>
                            <option value="Mayotte">Mayotte</option>
                            <option value="Mexico">Mexico</option>
                            <option value="Micronesia (Federated States of)">Micronesia (Federated States of)</option>
                            <option value="Moldova (the Republic of)">Moldova (the Republic of)</option>
                            <option value="Monaco">Monaco</option>
                            <option value="Mongolia">Mongolia</option>
                            <option value="Montenegro">Montenegro</option>
                            <option value="Montserrat">Montserrat</option>
                            <option value="Morocco">Morocco</option>
                            <option value="Mozambique">Mozambique</option>
                            <option value="Myanmar">Myanmar</option>
                            <option value="Namibia">Namibia</option>
                            <option value="Nauru">Nauru</option>
                            <option value="Nepal">Nepal</option>
                            <option value="Netherlands (the)">Netherlands (the)</option>
                            <option value="New Caledonia">New Caledonia</option>
                            <option value="New Zealand">New Zealand</option>
                            <option value="Nicaragua">Nicaragua</option>
                            <option value="Niger (the)">Niger (the)</option>
                            <option value="Nigeria">Nigeria</option>
                            <option value="Niue">Niue</option>
                            <option value="Norfolk Island">Norfolk Island</option>
                            <option value="Northern Mariana Islands (the)">Northern Mariana Islands (the)</option>
                            <option value="Norway">Norway</option>
                            <option value="Oman">Oman</option>
                            <option value="Pakistan">Pakistan</option>
                            <option value="Palau">Palau</option>
                            <option value="Palestine, State of">Palestine, State of</option>
                            <option value="Panama">Panama</option>
                            <option value="Papua New Guinea">Papua New Guinea</option>
                            <option value="Paraguay">Paraguay</option>
                            <option value="Peru">Peru</option>
                            <option value="Philippines (the)">Philippines (the)</option>
                            <option value="Pitcairn">Pitcairn</option>
                            <option value="Poland">Poland</option>
                            <option value="Portugal">Portugal</option>
                            <option value="Puerto Rico">Puerto Rico</option>
                            <option value="Qatar">Qatar</option>
                            <option value="Republic of North Macedonia">Republic of North Macedonia</option>
                            <option value="REUNION ISLAND">REUNION ISLAND</option>
                            <option value="Romania">Romania</option>
                            <option value="Russian Federation (the)">Russian Federation (the)</option>
                            <option value="Rwanda">Rwanda</option>
                            <option value="Saint Barthélemy">Saint Barthélemy</option>
                            <option value="Saint Helena, Ascension and Tristan da Cunha">Saint Helena, Ascension and Tristan da Cunha</option>
                            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                            <option value="Saint Lucia">Saint Lucia</option>
                            <option value="Saint Martin (French part)">Saint Martin (French part)</option>
                            <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                            <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                            <option value="Samoa">Samoa</option>
                            <option value="San Marino">San Marino</option>
                            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                            <option value="Saudi Arabia">Saudi Arabia</option>
                            <option value="Senegal">Senegal</option>
                            <option value="Serbia">Serbia</option>
                            <option value="Seychelles">Seychelles</option>
                            <option value="Sierra Leone">Sierra Leone</option>
                            <option value="Singapore">Singapore</option>
                            <option value="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
                            <option value="Slovakia">Slovakia</option>
                            <option value="Slovenia">Slovenia</option>
                            <option value="Solomon Islands">Solomon Islands</option>
                            <option value="Somalia">Somalia</option>
                            <option value="South Africa">South Africa</option>
                            <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                            <option value="South Sudan">South Sudan</option>
                            <option value="Spain">Spain</option>
                            <option value="Sri Lanka">Sri Lanka</option>
                            <option value="Sudan (the)">Sudan (the)</option>
                            <option value="Suriname">Suriname</option>
                            <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                            <option value="Sweden">Sweden</option>
                            <option value="Switzerland">Switzerland</option>
                            <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                            <option value="Taiwan (Province of China)">Taiwan (Province of China)</option>
                            <option value="Tajikistan">Tajikistan</option>
                            <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                            <option value="Thailand">Thailand</option>
                            <option value="Timor-Leste">Timor-Leste</option>
                            <option value="Togo">Togo</option>
                            <option value="Tokelau">Tokelau</option>
                            <option value="Tonga">Tonga</option>
                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                            <option value="Tunisia">Tunisia</option>
                            <option value="Turkey">Turkey</option>
                            <option value="Turkmenistan">Turkmenistan</option>
                            <option value="Turks and Caicos Islands (the)">Turks and Caicos Islands (the)</option>
                            <option value="Tuvalu">Tuvalu</option>
                            <option value="Uganda">Uganda</option>
                            <option value="Ukraine">Ukraine</option>
                            <option value="United Arab Emirates (the)">United Arab Emirates (the)</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="United States Minor Outlying Islands (the)">United States Minor Outlying Islands (the)</option>
                            <option value="United States of America (the)">United States of America (the)</option>
                            <option value="Uruguay">Uruguay</option>
                            <option value="Uzbekistan">Uzbekistan</option>
                            <option value="Vanuatu">Vanuatu</option>
                            <option value="Venezuela (Bolivarian Republic of)">Venezuela (Bolivarian Republic of)</option>
                            <option value="Viet Nam">Viet Nam</option>
                            <option value="Virgin Islands (British)">Virgin Islands (British)</option>
                            <option value="Virgin Islands (U.S.)">Virgin Islands (U.S.)</option>
                            <option value="Wallis and Futuna">Wallis and Futuna</option>
                            <option value="Western Sahara">Western Sahara</option>
                            <option value="Yemen">Yemen</option>
                            <option value="Zambia">Zambia</option>
                            <option value="Zimbabwe">Zimbabwe</option>
                        </select>
                </tr>
                <tr>
                    <th>Place of Birth (City, Country):  <span style="color: red;">*</span></th>
                    <td><input type="text" name="place_of_birth" placeholder="Place of Birth (City, Country)" class="required-field"></td>
                    <!-- <th>Marital Status:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="marital_status" placeholder="Marital Status" class="required-field"></td> -->
                    <th>Marital Status:  <span style="color: red;">*</span></th>
                    <td>
                        <select name="marital_status" class="required-field">
                            <option value="-SELECT -" selected disabled>- Select -</option>
                            <option value="Not Married">Not Married</option>
                            <option value="Married">Married</option>
                            <option value="Separated">Separated</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </td>

                    <th>No. of Children under 18: </th>
                    <td><input type="number" name="children_under_18" placeholder="No. of Children under 18"></td>
                </tr>
                <tr>
                    <th>Home Address:  <span style="color: red;">*</span></th>
                    <td colspan="3"><input type="text" name="home_address" placeholder="Home Address" class="required-field"></td>
                </tr>
                <tr>
                    <th>Home Zip:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="home_zip" placeholder="Home Zip" class="required-field"></td>
                    <th>Contact Phone:  <span style="color: red;">*</span></th>
                    <td><input type="tel" name="contact_phone" placeholder="Contact Phone" class="required-field"></td>
                </tr>
                <tr>
                    <th>Email:  <span style="color: red;">*</span></th>
                    <td><input type="email" name="email" id="email" placeholder="Email" class="required-field">
                    <span id="email-validation-message" style="color: red; font-size: 12px;"></span>
                    </td>
                    <th>Password:  <span style="color: red;">*</span></th>
                    <td><input type="password" name="password" placeholder="Password" class="required-field"></td>
                    <th>Skype/Telegram: </th>
                    <td><input type="text" name="skype_telegram" placeholder="Skype/Telegram"></td>
                </tr>
                <tr>
                    <th>Next of kin:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="next_kin" placeholder="Next of kin" class="required-field"></td>
                    <!-- <th>Relation:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="relation" placeholder="Relation" class="required-field"></td> -->
                    <th>Relation:  <span style="color: red;">*</span></th>
                    <td>
                        <select name="relation" class="required-field">
                            <option value="- Select -" selected disabled>- Select -</option>
                            <option value="Son">Son</option>
                            <option value="Wife">Wife</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Mother">Mother</option>
                            <option value="Father">Father</option>
                            <option value="Friend">Friend</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Next of kin’s address:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="next_kin_address" placeholder="Next of kin’s address" class="required-field"></td>
                    <th>Next of kin’s phone No  <span style="color: red;">*</span></th>
                    <td><input type="tel" name="next_kin_phone" placeholder="Next of kin’s phone No" class="required-field"></td>
                </tr>
                <tr>
                    <th>Height (cm):  <span style="color: red;">*</span></th>
                    <td><input type="number" name="height" placeholder="Height (cm)" class="required-field"></td>
                    <th>Weight (kg):  <span style="color: red;">*</span></th>
                    <td><input type="number" name="weight" placeholder="Weight (kg)" class="required-field"></td>
                    <!-- <th>Size of Overall (EUR):  <span style="color: red;">*</span></th>
                    <td><input type="text" name="size_overall" placeholder="Size of Overall (EUR)" class="required-field"></td> -->
                    <th>Size of Overall (EUR):  <span style="color: red;">*</span></th>
                    <td>
                        <select name="size_overall" class="required-field">
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <option value="XS-UK">XS-UK</option>
                            <option value="XS-EU">XS-EU</option>
                            <option value="XS-US">XS-US</option>
                            <option value="S-UK">S-UK</option>
                            <option value="S-EU">S-EU</option>
                            <option value="S-US">S-US</option>
                            <option value="M-UK">M-UK</option>
                            <option value="M-EU">M-EU</option>
                            <option value="M-US">M-US</option>
                            <option value="L-UK">L-UK</option>
                            <option value="L-EU">L-EU</option>
                            <option value="L-US">L-US</option>
                            <option value="XL-UK">XL-UK</option>
                            <option value="XL-EU">XL-EU</option>
                            <option value="XL-US">XL-US</option>
                            <option value="XXL-UK">XXL-UK</option>
                            <option value="XXL-EU">XXL-EU</option>
                            <option value="XXL-US">XXL-US</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Eye Color:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="eye_color" placeholder="Eye Color" class="required-field"></td>
                    <th>Hair Color:  <span style="color: red;">*</span></th>
                    <td><input type="text" name="hair_color" placeholder="Hair Color" class="required-field"></td>
                    <th>Shoes (EUR):  <span style="color: red;">*</span></th>
                    <td><input type="text" name="shoes" placeholder="Shoes (EUR)" class="required-field"></td>
                </tr>
            </table>
            </div>

            
             <div class="top_application form_outer">
            <h3>Marine Education</h3>
            <table>
                <tr>
                    <th>Name of maritime college or academy  <span style="color: red;">*</span></th>
                    <td><input type="text" name="maritime_college" placeholder="Name of maritime college or academy" class="required-field"></td>
                    <th>From  <span style="color: red;">*</span></th>
                    <td><input type="date" name="education_from" class="required-field"></td>
                </tr>
                <tr>
                    <th>Department  <span style="color: red;">*</span></th>
                    <td><input type="text" name="department" placeholder="Department" class="required-field"></td>
                    <th>Till  <span style="color: red;">*</span></th>
                    <td><input type="date" name="education_till" class="required-field"></td>
                </tr>
            </table>
            </div>
                 
            <div class="form_outer passports_certificates">
            <h3>Passports and Certificates</h3>
            <table>
                <tr>
                    <th>DOCUMENT</th>
                    <th>NUMBER</th>
                    <th>ISSUED DATE</th>
                    <th>VALID UNTIL</th>
                    <th>PLACE</th>
                </tr>
                <tr>
                    <td>TRAVEL PASSPORT:</td>
                    <td><input type="text" name="travel_passport_number" placeholder="Travel Passport"></td>
                    <td><input type="date" name="travel_passport_issued_date"></td>
                    <td><input type="date" name="travel_passport_valid_until"></td>
                    <td><input type="text" name="travel_passport_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>SEAMAN’S BOOK (SID):</td>
                    <td><input type="text" name="seaman_book_number" placeholder="Seaman's Book (SID)"></td>
                    <td><input type="date" name="seaman_book_issued_date"></td>
                    <td><input type="date" name="seaman_book_valid_until"></td>
                    <td><input type="text" name="seaman_book_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>CIVIL PASSPORT:</td>
                    <td><input type="text" name="civil_passport_number" placeholder="Civil Passport"></td>
                    <td><input type="date" name="civil_passport_issued_date"></td>
                    <td><input type="date" name="civil_passport_valid_until"></td>
                    <td><input type="text" name="civil_passport_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>U.S. VISA:</td>
                    <td><input type="text" name="us_visa_number" placeholder="U.S. Visa"></td>
                    <td><input type="date" name="us_visa_issued_date"></td>
                    <td><input type="date" name="us_visa_valid_until"></td>
                    <td><input type="text" name="us_visa_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>OTHER VALID VISA:</td>
                    <td><input type="text" name="other_visa_number" placeholder="Other Valid Visa"></td>
                    <td><input type="date" name="other_visa_issued_date"></td>
                    <td><input type="date" name="other_visa_valid_until"></td>
                    <td><input type="text" name="other_visa_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>CERTIFICATE OF COMPETENCY # 1</td>
                    <td><input type="text" name="certificate_of_competency1_number" placeholder="Certificate Of Competency #1"></td>
                    <td><input type="date" name="certificate_of_competency1_issued_date"></td>
                    <td><input type="date" name="certificate_of_competency1_valid_until"></td>
                    <td><input type="text" name="certificate_of_competency1_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <!-- <td>RANK / CAPACITY</td>
                    <td><input type="text" name="rank_capacity1_number" placeholder="Rank/Capacity"></td> -->
                    <td>RANK / CAPACITY</td>
                    <td>
                        <select name="rank_capacity1_number" required>
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <option value="#MST">#MST</option>
                            <option value="#CO">#CO</option>
                            <option value="#2O">#2O</option>
                            <option value="#3O">#3O</option>
                            <option value="#4O">#4O</option>
                            <option value="#JO">#JO</option>
                            <option value="#CE">#CE</option>
                            <option value="#2E">#2E</option>
                            <option value="#3E">#3E</option>
                            <option value="#4E">#4E</option>
                            <option value="#JE">#JE</option>
                            <option value="#EE">#EE</option>
                            <option value="#ELASS">#ELASS</option>
                            <option value="#DCADET">#DCADET</option>
                            <option value="#ECADET">#ECADET</option>
                            <option value="#ELCADET">#ELCADET</option>
                            <option value="#PMP">#PMP</option>
                            <option value="#BSN">#BSN</option>
                            <option value="#AB">#AB</option>
                            <option value="#OS">#OS</option>
                            <option value="#MTM">#MTM</option>
                            <option value="#WIPER">#WIPER</option>
                            <option value="#FTR">#FTR</option>
                            <option value="#WELDER">#WELDER</option>
                            <option value="#TURNER">#TURNER</option>
                            <option value="#MTM-TURNER">#MTM-TURNER</option>
                            <option value="#COOK">#COOK</option>
                            <option value="#MESS">#MESS</option>
                            <option value="#PAINTER">#PAINTER</option>
                            <option value="#TECHNICIAN">#TECHNICIAN</option>
                        </select>
                    </td>
                    <td><input type="date" name="rank_capacity1_issued_date"></td>
                    <td><input type="date" name="rank_capacity1_valid_until"></td>
                    <td><input type="text" name="rank_capacity1_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ENDORSEMENT OF CERTIFICATE # 1</td>
                    <td><input type="text" name="endorsement_of_certificate1_number" placeholder="Endorsement of Certificate #1"></td>
                    <td><input type="date" name="endorsement_of_certificate1_issued_date"></td>
                    <td><input type="date" name="endorsement_of_certificate1_valid_until"></td>
                    <td><input type="text" name="endorsement_of_certificate1_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>CERTIFICATE OF COMPETENCY # 2</td>
                    <td><input type="text" name="certificate_of_competency2_number" placeholder="Certificate of Competency #2"></td>
                    <td><input type="date" name="certificate_of_competency2_issued_date"></td>
                    <td><input type="date" name="certificate_of_competency2_valid_until"></td>
                    <td><input type="text" name="certificate_of_competency2_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <!-- <td>RANK / CAPACITY</td>
                    <td><input type="text" name="rank_capacity2_number" placeholder="Rank/Capacity"></td> -->
                    <td>RANK / CAPACITY</td>
                    <td>
                        <select name="rank_capacity2_number" required>
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <option value="#MST">#MST</option>
                            <option value="#CO">#CO</option>
                            <option value="#2O">#2O</option>
                            <option value="#3O">#3O</option>
                            <option value="#4O">#4O</option>
                            <option value="#JO">#JO</option>
                            <option value="#CE">#CE</option>
                            <option value="#2E">#2E</option>
                            <option value="#3E">#3E</option>
                            <option value="#4E">#4E</option>
                            <option value="#JE">#JE</option>
                            <option value="#EE">#EE</option>
                            <option value="#ELASS">#ELASS</option>
                            <option value="#DCADET">#DCADET</option>
                            <option value="#ECADET">#ECADET</option>
                            <option value="#ELCADET">#ELCADET</option>
                            <option value="#PMP">#PMP</option>
                            <option value="#BSN">#BSN</option>
                            <option value="#AB">#AB</option>
                            <option value="#OS">#OS</option>
                            <option value="#MTM">#MTM</option>
                            <option value="#WIPER">#WIPER</option>
                            <option value="#FTR">#FTR</option>
                            <option value="#WELDER">#WELDER</option>
                            <option value="#TURNER">#TURNER</option>
                            <option value="#MTM-TURNER">#MTM-TURNER</option>
                            <option value="#COOK">#COOK</option>
                            <option value="#MESS">#MESS</option>
                            <option value="#PAINTER">#PAINTER</option>
                            <option value="#TECHNICIAN">#TECHNICIAN</option>
                        </select>
                    </td>
                    <td><input type="date" name="rank_capacity2_issued_date"></td>
                    <td><input type="date" name="rank_capacity2_valid_until"></td>
                    <td><input type="text" name="rank_capacity2_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ENDORSEMENT OF CERTIFICATE # 2</td>
                    <td><input type="text" name="endorsement_of_certificate2_number" placeholder="Endorsement of Certificate #2"></td>
                    <td><input type="date" name="endorsement_of_certificate2_issued_date"></td>
                    <td><input type="date" name="endorsement_of_certificate2_valid_until"></td>
                    <td><input type="text" name="endorsement_of_certificate2_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>GMDSS CERTIFICATE/ENDORSEMENT</td>
                    <td><input type="text" name="gmdss_certificate_endorsement_number" placeholder="GMDSS Certificate/Endorsement"></td>
                    <td><input type="date" name="gmdss_certificate_endorsement_issued_date"></td>
                    <td><input type="date" name="gmdss_certificate_endorsement_valid_until"></td>
                    <td><input type="text" name="gmdss_certificate_endorsement_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>BASIC SAFETY TRAINING</td>
                    <td><input type="text" name="basic-safety_training_number" placeholder="Basic Safety Training"></td>
                    <td><input type="date" name="basic-safety_training_issued_date"></td>
                    <td><input type="date" name="basic-safety_training_valid_until"></td>
                    <td><input type="text" name="basic-safety_training_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>PROFICIENCY IN SURVIVAL CRAFT</td>
                    <td><input type="text" name="proficiency_in_survival_craft_number" placeholder="Proficiency in Survival Craft"></td>
                    <td><input type="date" name="proficiency_in_survival_craft_issued_date"></td>
                    <td><input type="date" name="proficiency_in_survival_craft_valid_until"></td>
                    <td><input type="text" name="proficiency_in_survival_craft_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ADVANCED FIRE FIGHTING</td>
                    <td><input type="text" name="advanced_fire_fighting_number" placeholder="Advanced Fire Fighting"></td>
                    <td><input type="date" name="advanced_fire_fighting_issued_date"></td>
                    <td><input type="date" name="advanced_fire_fighting_valid_until"></td>
                    <td><input type="text" name="advanced_fire_fighting_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>MEDICAL FIRST AID</td>
                    <td><input type="text" name="medical_first_aid_number" placeholder="Medical First Aid"></td>
                    <td><input type="date" name="medical_first_aid_issued_date"></td>
                    <td><input type="date" name="medical_first_aid_valid_until"></td>
                    <td><input type="text" name="medical_first_aid_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>MEDICAL CARE</td>
                    <td><input type="text" name="medical_care_number" placeholder="Medical Care"></td>
                    <td><input type="date" name="medical_care_issued_date"></td>
                    <td><input type="date" name="medical_care_valid_until"></td>
                    <td><input type="text" name="medical_care_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>SHIPS SECURITY OFFICER</td>
                    <td><input type="text" name="ships_security_officer_number" placeholder="Ships Security Officer"></td>
                    <td><input type="date" name="ships_security_officer_issued_date"></td>
                    <td><input type="date" name="ships_security_officer_valid_until"></td>
                    <td><input type="text" name="ships_security_officer_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>DESIGNATED SECURITY DUTIES</td>
                    <td><input type="text" name="designated_security_duties_number" placeholder="Designated Security Duties"></td>
                    <td><input type="date" name="designated_security_duties_issued_date"></td>
                    <td><input type="date" name="designated_security_duties_valid_until"></td>
                    <td><input type="text" name="designated_security_duties_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>SECURITY AWARENESS</td>
                    <td><input type="text" name="security_awareness_number" placeholder="Security Awareness"></td>
                    <td><input type="date" name="security_awareness_issued_date"></td>
                    <td><input type="date" name="security_awareness_valid_until"></td>
                    <td><input type="text" name="security_awareness_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>SHIPS SAFETY OFFICER / ISM</td>
                    <td><input type="text" name="ships_safety_officer_ism_number" placeholder="Ships Safety Officer/ISM"></td>
                    <td><input type="date" name="ships_safety_officer_ism_issued_date"></td>
                    <td><input type="date" name="ships_safety_officer_ism_valid_until"></td>
                    <td><input type="text" name="ships_safety_officer_ism_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>RADAR NAVIGATION, RADAR PLOTTING AND USE OF ARPA</td>
                    <td><input type="text" name="radar_navigation_number" placeholder="Radar Navigation, Radar Plotting & Use of APRA"></td>
                    <td><input type="date" name="radar_navigation_issued_date"></td>
                    <td><input type="date" name="radar_navigation_valid_until"></td>
                    <td><input type="text" name="radar_navigation_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>DANGEROUS & HAZARDOUS CARGOES</td>
                    <td><input type="text" name="dangerous_cergoes_number" placeholder="Dangerous & Hazardous Cargoes"></td>
                    <td><input type="date" name="dangerous_cergoes_issued_date"></td>
                    <td><input type="date" name="dangerous_cergoes_valid_until"></td>
                    <td><input type="text" name="dangerous_cergoes_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>BRIDGE TEAM MNGT</td>
                    <td><input type="text" name="bridge_ream_mingt_number" placeholder="Bridge Team Mngt"></td>
                    <td><input type="date" name="bridge_ream_mingt_issued_date"></td>
                    <td><input type="date" name="bridge_ream_mingt_valid_until"></td>
                    <td><input type="text" name="bridge_ream_mingt_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ENGINE ROOM RESOURCE MNGT</td>
                    <td><input type="text" name="engine_room_resource_mngt_number" placeholder="Engine Room Resource Mngt"></td>
                    <td><input type="date" name="engine_room_resource_mngt_issued_date"></td>
                    <td><input type="date" name="engine_room_resource_mngt_valid_until"></td>
                    <td><input type="text" name="engine_room_resource_mngt_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ECDIS GENERIC</td>
                    <td><input type="text" name="ecdis_generic_number" placeholder="ECDIS Generic"></td>
                    <td><input type="date" name="ecdis_generic_issued_date"></td>
                    <td><input type="date" name="ecdis_generic_valid_until"></td>
                    <td><input type="text" name="ecdis_generic_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ECDIS SPECIFIC</td>
                    <td><input type="text" name="ecdis_specific_number" placeholder="ECDIS Specific"></td>
                    <td><input type="date" name="ecdis_specific_issued_date"></td>
                    <td><input type="date" name="ecdis_specific_valid_until"></td>
                    <td><input type="text" name="ecdis_specific_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>BASIC TRAINING FOR OIL & CHEMICAL TANKER CERTIFICATE</td>
                    <td><input type="text" name="basic_training_for_oil_number" placeholder="Basic Training For Oil & Chemical Tanker Certificate"></td>
                    <td><input type="date" name="basic_training_for_oil_issued_date"></td>
                    <td><input type="date" name="basic_training_for_oil_valid_until"></td>
                    <td><input type="text" name="basic_training_for_oil_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ADV. TRAINING FOR OIL TANKER CERTIFICATE</td>
                    <td><input type="text" name="adv_training_for_oil_number" placeholder="Adv. Training For Oil Certificate"></td>
                    <td><input type="date" name="adv_training_for_oil_issued_date"></td>
                    <td><input type="date" name="adv_training_for_oil_valid_until"></td>
                    <td><input type="text" name="adv_training_for_oil_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ADV. TRAINING FOR CHEMICAL TANKER CERTIFICATE</td>
                    <td><input type="text" name="adv_training_for_chemical_number" placeholder="Adv. Training For Chemical Tanker Certificate"></td>
                    <td><input type="date" name="adv_training_for_chemical_issued_date"></td>
                    <td><input type="date" name="adv_training_for_chemical_valid_until"></td>
                    <td><input type="text" name="adv_training_for_chemical_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>BASIC TRAINING FOR OIL AND CHEMICAL TANKER - ENDORSEMENT</td>
                    <td><input type="text" name="basic_training_for_oil_endorsement_number" placeholder="Basic Training For Oil & Chemical Tanker - Endorsement"></td>
                    <td><input type="date" name="basic_training_for_oil_endorsement_issued_date"></td>
                    <td><input type="date" name="basic_training_for_oil_endorsement_valid_until"></td>
                    <td><input type="text" name="basic_training_for_oil_endorsement_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ADV. TRAINING FOR OIL TANKER -ENDORSEMENT</td>
                    <td><input type="text" name="adv_training_for_oil_endorsement_number" placeholder="Adv. Training For Oil Tanker - Endorsement"></td>
                    <td><input type="date" name="adv_training_for_oil_endorsement_issued_date"></td>
                    <td><input type="date" name="adv_training_for_oil_endorsement_valid_until"></td>
                    <td><input type="text" name="adv_training_for_oil_endorsement_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>ADV. TRAINING FOR CHEMICAL TANKER - ENDORSEMENT</td>
                    <td><input type="text" name="adv_training_for_chemical_endorsement_number" placeholder="Adv. Training For Chemical Tanker"></td>
                    <td><input type="date" name="adv_training_for_chemical_endorsement_issued_date"></td>
                    <td><input type="date" name="adv_training_for_chemical_endorsement_valid_until"></td>
                    <td><input type="text" name="adv_training_for_chemical_endorsement_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>BASIC/ADV. TRAINING FOR GAS TANKER ENDO</td>
                    <td><input type="text" name="basic_adv_training_number" placeholder="Basic/Adv. Training For Gas Tanker Endo"></td>
                    <td><input type="date" name="basic_adv_training_issued_date"></td>
                    <td><input type="date" name="basic_adv_training_valid_until"></td>
                    <td><input type="text" name="basic_adv_training_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>HIGH VOLTAGE EL. EQUIPMENT</td>
                    <td><input type="text" name="high_voltage_number" placeholder="High Voltage El. Equipment"></td>
                    <td><input type="date" name="high_voltage_issued_date"></td>
                    <td><input type="date" name="high_voltage_valid_until"></td>
                    <td><input type="text" name="high_voltage_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>COOK CERTIFICATE</td>
                    <td><input type="text" name="cook_certificate_number" placeholder="Cook Certificate"></td>
                    <td><input type="date" name="cook_certificate_issued_date"></td>
                    <td><input type="date" name="cook_certificate_valid_until"></td>
                    <td><input type="text" name="cook_certificate_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>MESSMAN (MLC-2006)</td>
                    <td><input type="text" name="messman_number" placeholder="Messman (MLC-2006)"></td>
                    <td><input type="date" name="messman_issued_date"></td>
                    <td><input type="date" name="messman_valid_until"></td>
                    <td><input type="text" name="messman_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>YELLOW FEVER CERTIFICATE</td>
                    <td><input type="text" name="yellow_fever_certificate_number" placeholder="Yellow Fever Certificate"></td>
                    <td><input type="date" name="yellow_fever_certificate_issued_date"></td>
                    <td><input type="date" name="yellow_fever_certificate_valid_until"></td>
                    <td><input type="text" name="yellow_fever_certificate_place" placeholder="Place"></td>
                </tr>
                <tr>
                    <td>COVID-19 VACCINATION CERTIFICATE</td>
                    <td><input type="text" name="covid_19_vaccination_certificate_number" placeholder="Covid 19 Vaccination Certificate"></td>
                    <td><input type="date" name="covid_19_vaccination_certificate_issued_date"></td>
                    <td><input type="date" name="covid_19_vaccination_certificate_valid_until"></td>
                    <td><input type="text" name="covid_19_vaccination_certificate_place" placeholder="Place"></td>
                </tr>
            </table>
            </div>
                 
            <div class="form_outer  record_book_outer">
            <h3>FOREIGN SEAMAN’S ID / RECORD BOOKS</h3>
            <table id="seaman-records-table">
                <thead>
                    <tr>
                        <th>FLAG</th>
                        <th>NUMBER</th>
                        <th>ISSUED DATE</th>
                        <th>VALID UNTIL</th>
                        <th>PLACE</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="record-row">
                        <!-- <td><input type="text" name="flag[]" placeholder="Flag"></td> -->
                        <td>
                            <select name="flag[]" >
                                <option value="- Select -" selected disabled>- Select -</option>
                                <option value="Afghanistan">Afghanistan</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antarctica">Antarctica</option>
                                <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas (The)">Bahamas (The)</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia (Plurinational State of)">Bolivia (Plurinational State of)</option>
                                <option value="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
                                <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Bouvet Island">Bouvet Island</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Territory (the)">British Indian Ocean Territory (the)</option>
                                <option value="Brunei Darussalam">Brunei Darussalam</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cabo Verde">Cabo Verde</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Cayman Islands (the)">Cayman Islands (the)</option>
                                <option value="Central African Republic (the)">Central African Republic (the)</option>
                                <option value="Chad">Chad</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos (Keeling) Islands (the)">Cocos (Keeling) Islands (the)</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros (the)">Comoros (the)</option>
                                <option value="Congo (the Democratic Republic of the)">Congo (the Democratic Republic of the)</option>
                                <option value="Congo (the)">Congo (the)</option>
                                <option value="Cook Islands (the)">Cook Islands (the)</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote d Ivoire">Cote d Ivoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaçao">Curaçao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czechia">Czechia</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic (the)">Dominican Republic (the)</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Eswatini">Eswatini</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="European Union">European Union</option>
                                <option value="Falkland Islands (the) [Malvinas]">Falkland Islands (the) [Malvinas]</option>
                                <option value="Faroe Islands (the)">Faroe Islands (the)</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Territories (the)">French Southern Territories (the)</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia (the)">Gambia (the)</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guernsey">Guernsey</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guinea-Bissau">Guinea-Bissau</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
                                <option value="Holy See (the)">Holy See (the)</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="India">India</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Iran (Islamic Republic of)">Iran (Islamic Republic of)</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jersey">Jersey</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea (the Democratic Peoples Republic of)">Korea (the Democratic Peoples Republic of)</option>
                                <option value="Korea (the Republic of)">Korea (the Republic of)</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Lao Peoples Democratic Republic (the)">Lao Peoples Democratic Republic (the)</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macao">Macao</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands (the)">Marshall Islands (the)</option>
                                <option value="Martial Island">Martial Island</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Micronesia (Federated States of)">Micronesia (Federated States of)</option>
                                <option value="Moldova (the Republic of)">Moldova (the Republic of)</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montenegro">Montenegro</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Namibia">Namibia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherlands (the)">Netherlands (the)</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger (the)">Niger (the)</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Northern Mariana Islands (the)">Northern Mariana Islands (the)</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau">Palau</option>
                                <option value="Palestine, State of">Palestine, State of</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Philippines (the)">Philippines (the)</option>
                                <option value="Pitcairn">Pitcairn</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of North Macedonia">Republic of North Macedonia</option>
                                <option value="REUNION ISLAND">REUNION ISLAND</option>
                                <option value="Romania">Romania</option>
                                <option value="Russian Federation (the)">Russian Federation (the)</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="Saint Barthélemy">Saint Barthélemy</option>
                                <option value="Saint Helena, Ascension and Tristan da Cunha">Saint Helena, Ascension and Tristan da Cunha</option>
                                <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                <option value="Saint Lucia">Saint Lucia</option>
                                <option value="Saint Martin (French part)">Saint Martin (French part)</option>
                                <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                <option value="Samoa">Samoa</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Serbia">Serbia</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                                <option value="South Sudan">South Sudan</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan (the)">Sudan (the)</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                <option value="Taiwan (Province of China)">Taiwan (Province of China)</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Timor-Leste">Timor-Leste</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks and Caicos Islands (the)">Turks and Caicos Islands (the)</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Emirates (the)">United Arab Emirates (the)</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="United States Minor Outlying Islands (the)">United States Minor Outlying Islands (the)</option>
                                <option value="United States of America (the)">United States of America (the)</option>
                                <option value="Uruguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Venezuela (Bolivarian Republic of)">Venezuela (Bolivarian Republic of)</option>
                                <option value="Viet Nam">Viet Nam</option>
                                <option value="Virgin Islands (British)">Virgin Islands (British)</option>
                                <option value="Virgin Islands (U.S.)">Virgin Islands (U.S.)</option>
                                <option value="Wallis and Futuna">Wallis and Futuna</option>
                                <option value="Western Sahara">Western Sahara</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                        </td>
                        <td><input type="text" name="flag_number[]" placeholder="Flag Number"></td>
                        <td><input type="date" name="issued_date[]"></td>
                        <td><input type="date" name="valid_until[]"></td>
                        <td><input type="text" name="place[]" placeholder="Place"></td>
                        <td><button type="button" class="remove-row">Remove</button></td>
                    </tr>
                </tbody>
            </table>
           <div class="add_row"><button type="button" id="add-row">Add Row</button></div> 
                 </div>

                 
            <div class="form_outer sea_services">
            <h3>PREVIOUS SEA SERVICE</h3>
            <div class="table_inner">
            <table id="sea-service-table">
                <thead>
                    <tr>
                        <th>FROM</th>
                        <th>TO</th>
                        <th>POSITION</th>
                        <th>SALARY</th>
                        <th>NAME OF VESSEL</th>
                        <th>SHIPOWNER</th>
                        <th>TYPE OF VESSEL</th>
                        <th>TYPE OF ENGINE</th>
                        <th>BUILD YEAR</th>
                        <th>DWT</th>
                        <th>BHP</th>
                        <th>FLAG</th>
                        <th>CREWING AGENT</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="date" name="from_date[]"></td>
                        <td><input type="date" name="to_date[]"></td>
                        <!-- <td><input type="text" name="position[]" placeholder="Position"></td> -->
                        <td>
                        <select name="position[]">
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <option value="MAST">MAST</option>
                            <option value="CE">CE</option>
                            <option value="CO">CO</option>
                            <option value="2O">2O</option>
                            <option value="2E">2E</option>
                            <option value="3E">3E</option>
                            <option value="3O">3O</option>
                            <option value="EE">EE</option>
                            <option value="4E">4E</option>
                            <option value="PUMP">PUMP</option>
                            <option value="BOSUN">BOSUN</option>
                            <option value="AB">AB</option>
                            <option value="OS">OS</option>
                            <option value="DC">DC</option>
                            <option value="MTM">MTM</option>
                            <option value="MTM TURNER">MTM TURNER</option>
                            <option value="WIPER">WIPER</option>
                            <option value="FITTER">FITTER</option>
                            <option value="COOK">COOK</option>
                            <option value="MESS">MESS</option>
                            <option value="SUPER">SUPER</option>
                            <option value="JO">JO</option>
                            <option value="JE">JE</option>
                            <option value="ETO">ETO</option>
                            <option value="ETO AS">ETO AS</option>
                            <option value="EC">EC</option>
                            <option value="OIL">OIL</option>
                            <option value="PAINT">PAINT</option>
                            <option value="ELECAD">ELECAD</option>
                            </select>
                        </td>
                        <td><input type="text" name="salary[]" placeholder="Salary"></td>
                        <td><input type="text" name="name_vessel[]" placeholder="Name of Vessel"></td>
                        <td><input type="text" name="ship_owner[]" placeholder="Ship Owner"></td>
                        <!-- <td><input type="text" name="type_vessel[]" placeholder="Type of Vessel"></td> -->
                        <td>
                        <select name="type_vessel[]">
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <option value="PASSENGER">PASSENGER</option>
                            <option value="OTHER">OTHER</option>
                            <option value="CONTAINER">CONTAINER</option>
                            <option value="LNG">LNG</option>
                            <option value="BULK CARRIER">BULK CARRIER</option>
                            <option value="CABLE SHIP">CABLE SHIP</option>
                            <option value="GAS">GAS</option>
                            <option value="TANKER">TANKER</option>
                            <option value="PURE CAR CARRIER">PURE CAR CARRIER</option>
                            <option value="WOOD CHIP">WOOD CHIP</option>
                            <option value="GENERAL CARGO">GENERAL CARGO</option>
                            <option value="NON CARGO SHIP">NON CARGO SHIP</option>
                            <option value="ROLL ON ROLL OFF">ROLL ON ROLL OFF</option>
                            <option value="SPECIALIZED VESSEL">SPECIALIZED VESSEL</option>
                            <option value="CHEMICAL">CHEMICAL</option>
                            <option value="OIL">OIL</option>
                            <option value="OIL AND CHEM">OIL AND CHEM</option>
                            <option value="TRAINING SHIP">TRAINING SHIP</option>
                            <option value="HEAVY LIFT">HEAVY LIFT</option>
                            <option value="OTHER DRY">OTHER DRY</option>
                            <option value="CYRIL VESSEL">CYRIL VESSEL</option>
                            <option value="REEFER">REEFER</option>
                            <option value="TUG">TUG</option>
                            <option value="Survery">Survery</option>
                            <option value="SAIL SHIP">SAIL SHIP</option>
                            </select>
                        </td>
                        <!-- <td><input type="text" name="type_engine[]" placeholder="Type of Engine"></td> -->
                        <td>
                        <select name="type_engine[]">
                        <option value="- SELECT -" selected disabled>- Select -</option>
                        <option value="MAN Diesel &amp; Turbo">MAN Diesel &amp; Turbo</option>
                        <option value="Wärtsilä">Wärtsilä</option>
                        <option value="Caterpillar">Caterpillar</option>
                        <option value="Rolls-Royce ">Rolls-Royce </option>
                        <option value="Kongsberg">Kongsberg</option>
                        <option value="General Electric">General Electric</option>
                        <option value="ABB (Azipod)">ABB (Azipod)</option>
                        <option value="Cummins">Cummins</option>
                        <option value="Yanmar">Yanmar</option>
                        <option value="Hyundai">Hyundai</option>
                        <option value="Daihatsu">Daihatsu</option>
                        <option value="Volvo Penta">Volvo Penta</option>
                        <option value="Deutz">Deutz</option>
                        <option value="Doosan">Doosan</option>
                        <option value="Mitsubishi">Mitsubishi</option>
                        <option value="SKL Diesel">SKL Diesel</option>
                        <option value="Baudouin">Baudouin</option>
                        <option value="Pielstick">Pielstick</option>
                        <option value="Scania">Scania</option>
                        </select>
                        </td>
                        <td><input type="text" name="build_year[]" placeholder="Build Year"></td>
                        <td><input type="text" name="dwt[]" placeholder="DWT"></td>
                        <td><input type="text" name="bhp[]" placeholder="BHP"></td>
                        <!-- <td><input type="text" name="flag[]" placeholder="Flag"></td> -->
                        <td>
                            <select name="flag[]" >
                                <option value="- Select -" selected disabled>- Select -</option>
                                <option value="Afghanistan">Afghanistan</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antarctica">Antarctica</option>
                                <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas (The)">Bahamas (The)</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia (Plurinational State of)">Bolivia (Plurinational State of)</option>
                                <option value="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
                                <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Bouvet Island">Bouvet Island</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Territory (the)">British Indian Ocean Territory (the)</option>
                                <option value="Brunei Darussalam">Brunei Darussalam</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cabo Verde">Cabo Verde</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Cayman Islands (the)">Cayman Islands (the)</option>
                                <option value="Central African Republic (the)">Central African Republic (the)</option>
                                <option value="Chad">Chad</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos (Keeling) Islands (the)">Cocos (Keeling) Islands (the)</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros (the)">Comoros (the)</option>
                                <option value="Congo (the Democratic Republic of the)">Congo (the Democratic Republic of the)</option>
                                <option value="Congo (the)">Congo (the)</option>
                                <option value="Cook Islands (the)">Cook Islands (the)</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote d Ivoire">Cote d Ivoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaçao">Curaçao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czechia">Czechia</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic (the)">Dominican Republic (the)</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Eswatini">Eswatini</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="European Union">European Union</option>
                                <option value="Falkland Islands (the) [Malvinas]">Falkland Islands (the) [Malvinas]</option>
                                <option value="Faroe Islands (the)">Faroe Islands (the)</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Territories (the)">French Southern Territories (the)</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia (the)">Gambia (the)</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guernsey">Guernsey</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guinea-Bissau">Guinea-Bissau</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
                                <option value="Holy See (the)">Holy See (the)</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="India">India</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Iran (Islamic Republic of)">Iran (Islamic Republic of)</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jersey">Jersey</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea (the Democratic Peoples Republic of)">Korea (the Democratic Peoples Republic of)</option>
                                <option value="Korea (the Republic of)">Korea (the Republic of)</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Lao Peoples Democratic Republic (the)">Lao Peoples Democratic Republic (the)</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macao">Macao</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands (the)">Marshall Islands (the)</option>
                                <option value="Martial Island">Martial Island</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Micronesia (Federated States of)">Micronesia (Federated States of)</option>
                                <option value="Moldova (the Republic of)">Moldova (the Republic of)</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montenegro">Montenegro</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Namibia">Namibia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherlands (the)">Netherlands (the)</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger (the)">Niger (the)</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Northern Mariana Islands (the)">Northern Mariana Islands (the)</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau">Palau</option>
                                <option value="Palestine, State of">Palestine, State of</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Philippines (the)">Philippines (the)</option>
                                <option value="Pitcairn">Pitcairn</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of North Macedonia">Republic of North Macedonia</option>
                                <option value="REUNION ISLAND">REUNION ISLAND</option>
                                <option value="Romania">Romania</option>
                                <option value="Russian Federation (the)">Russian Federation (the)</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="Saint Barthélemy">Saint Barthélemy</option>
                                <option value="Saint Helena, Ascension and Tristan da Cunha">Saint Helena, Ascension and Tristan da Cunha</option>
                                <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                <option value="Saint Lucia">Saint Lucia</option>
                                <option value="Saint Martin (French part)">Saint Martin (French part)</option>
                                <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                <option value="Samoa">Samoa</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Serbia">Serbia</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Sint Maarten (Dutch part)">Sint Maarten (Dutch part)</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                                <option value="South Sudan">South Sudan</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan (the)">Sudan (the)</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                                <option value="Taiwan (Province of China)">Taiwan (Province of China)</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Timor-Leste">Timor-Leste</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks and Caicos Islands (the)">Turks and Caicos Islands (the)</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Emirates (the)">United Arab Emirates (the)</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="United States Minor Outlying Islands (the)">United States Minor Outlying Islands (the)</option>
                                <option value="United States of America (the)">United States of America (the)</option>
                                <option value="Uruguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Venezuela (Bolivarian Republic of)">Venezuela (Bolivarian Republic of)</option>
                                <option value="Viet Nam">Viet Nam</option>
                                <option value="Virgin Islands (British)">Virgin Islands (British)</option>
                                <option value="Virgin Islands (U.S.)">Virgin Islands (U.S.)</option>
                                <option value="Wallis and Futuna">Wallis and Futuna</option>
                                <option value="Western Sahara">Western Sahara</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                        </td>
                        <td><input type="text" name="crewing_agent[]" placeholder="Crewing Agent"></td>
                        <td><button type="button" class="remove-row-second">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="add_row">  <button type="button" id="add-row-second">Add Row</button></div>
            </div>
                  
             <div class="form_outer brif_info">
              <h3>BRIEF INFORMATION ABOUT PREVIOUS EMPLOYERS</h3>
              <table id="employers-table">
                <thead>
                    <tr>
                        <th>COMPANY</th>
                        <th>PERSON IN CHARGE</th>
                        <th>CONTACT DETAILS (Phone Number, e-mail)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="company[]" placeholder="Company"></td>
                        <td><input type="text" name="person_in_charge[]" placeholder="Person In Charge"></td>
                        <td><input type="text" name="contact_details[]" placeholder="Contact Details (Phone Number, e-mail)"></td>
                        <td><button type="button" class="remove-row-third">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="add-row-third">Add Row</button>
             
         
            </div>
            <div class="hereby_confirm">
           <p> <input type="checkbox" id="confirmationCheckbox"> I hereby confirm that above information is true and correct to the best of my knowledge. I understand that this information will be held in the computer database due to my real or possible
employment. Signing it, I willfully give my permission to collect and process my personal information and to use it in all and legal way. I give my permission for my personal information to be provided to the possible employers and any other persons, if such need arises for my employment. Besides, I permit the STELLA MARIS SHIPPING employees to request personal information (data) about me from my former employers.</p>
          
          <div class="date-sign-submit">
          <div class="date-sign">
                Date:<input type="date" name="date_signed" class="required-field">
                Signature:<input type="text" name="name_sign" class="required-field">
            </div>
            <div id="error-message" style="color: red; display: none; margin-bottom: 10px;">
                Please fill all required fields and check the confirmation checkbox.
            </div>
            <input type="submit" name="submit_application_form" value="Submit">
            </div>
        </div>
        </div>
    </form>
    <script>
        document.getElementById("applicationForm").addEventListener("submit", function(event) {
            const errorMessage = document.getElementById("error-message");
            let isValid = true;

            // Check required fields
            const requiredFields = document.querySelectorAll(".required-field");
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                }
            });

            // Check confirmation checkbox
            const confirmationCheckbox = document.getElementById("confirmationCheckbox");
            if (!confirmationCheckbox.checked) {
                isValid = false;
            }

            // if (!isValid) {
            //     event.preventDefault(); // Prevent form submission
            //     errorMessage.style.display = "block"; // Show error message
            // } else {
            //     errorMessage.style.display = "none"; // Hide error message
            // }
             if (!isValid) {
        event.preventDefault(); // Prevent form submission
        errorMessage.style.display = "block"; // Show error message

        // Hide error message after 5 seconds
        setTimeout(function() {
            errorMessage.style.display = "none";
        }, 5000);
    } else {
        errorMessage.style.display = "none"; // Hide error message
    }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('wpmsf_application_form', 'wpmsf_display_application_form');

// Handle Form Submission
function wpmsf_handle_application_submission() {
    if (isset($_POST['submit_application_form'])) {
        global $wpdb;

        // Insert data into the primary table
        $form_table_name = $wpdb->prefix . 'form_data';
        $form_data = [
            'position_applied' => sanitize_text_field($_POST['position_applied']),
            'date_of_readiness' => sanitize_text_field($_POST['date_of_readiness']),
            'surname' => sanitize_text_field($_POST['surname']),
            'name' => sanitize_text_field($_POST['name']),
            'father_name' => sanitize_text_field($_POST['father_name']),
            'mother_name' => sanitize_text_field($_POST['mother_name']),
            'date_of_birth' => sanitize_text_field($_POST['date_of_birth']),
            'nationality' => sanitize_text_field($_POST['nationality']),
            'place_of_birth' => sanitize_text_field($_POST['place_of_birth']),
            'marital_status' => sanitize_text_field($_POST['marital_status']),
            'children_under_18' => intval($_POST['children_under_18']),
            'home_address' => sanitize_textarea_field($_POST['home_address']),
            'home_zip' => sanitize_text_field($_POST['home_zip']),
            'contact_phone' => sanitize_text_field($_POST['contact_phone']),
            'email' => sanitize_email($_POST['email']),
            'password' => sanitize_text_field($_POST['password']),
            'skype_telegram' => sanitize_text_field($_POST['skype_telegram']),
            'next_kin' => sanitize_text_field($_POST['next_kin']),
            'relation' => sanitize_text_field($_POST['relation']),
            'next_kin_address' => sanitize_text_field($_POST['next_kin_address']),
            'next_kin_phone' => sanitize_text_field($_POST['next_kin_phone']),
            'height' => intval($_POST['height']),
            'weight' => intval($_POST['weight']),
            'size_overall' => sanitize_text_field($_POST['size_overall']),
            'eye_color' => sanitize_text_field($_POST['eye_color']),
            'hair_color' => sanitize_text_field($_POST['hair_color']),
            'shoes' => sanitize_text_field($_POST['shoes']),
            'maritime_college' => sanitize_text_field($_POST['maritime_college']),
            'department' => sanitize_text_field($_POST['department']),
            'education_from' => sanitize_text_field($_POST['education_from']),
            'education_till' => sanitize_text_field($_POST['education_till']),
            'date_signed'   => sanitize_text_field($_POST['date_of_readiness']),
            'name_sign' => sanitize_text_field($_POST['date_of_readiness']),
        ];

        $wpdb->insert($form_table_name, $form_data);
        $user_id = $wpdb->insert_id; // Get the ID of the inserted record

        // Create WordPress user in wp_users table
        if ($user_id) {
            $user_login = sanitize_text_field($_POST['name']); // User login as name
            $user_email = sanitize_email($_POST['email']); // User email
            $user_password = sanitize_text_field($_POST['password']); // Plain password

            // Insert into wp_users
            $user_data = [
                'user_login' => $user_login,
                'user_pass' => $user_password,
                'user_email' => $user_email,
                'user_nicename' => $user_login, // Nicename as the name
                'display_name' => $user_login,  // Display name as the name
                'user_registered' => current_time('mysql'),
            ];

            $wp_user_id = wp_insert_user($user_data);

            if (!is_wp_error($wp_user_id)) {

                // Now, update the form_data table to store user_id_users
            $wpdb->update(
                $form_table_name,
                ['user_id_users' => $wp_user_id],  // Set the user_id_users field
                ['id' => $user_id]  // Ensure it's for the correct form data
            );
                // Insert into application_data table
            $application_table_name = $wpdb->prefix . 'application_data';

            // Define a pattern for dynamic document fields
            $document_types = [
                'travel_passport' => 'TRAVEL PASSPORT',
                'seaman_book' => 'SEAMAN’S BOOK (SID)',
                'civil_passport' => 'CIVIL PASSPORT',
                'us_visa' => 'U.S. VISA',
                'other_visa' => 'OTHER VALID VISA',
                'certificate_of_competency1' => 'CERTIFICATE OF COMPETENCY # 1',
                'rank_capacity1' => 'RANK / CAPACITY',
                'endorsement_of_certificate1' => 'ENDORSEMENT OF CERTIFICATE # 1',
                'certificate_of_competency2' => 'CERTIFICATE OF COMPETENCY # 2',
                'rank_capacity2' => 'RANK / CAPACITY',
                'endorsement_of_certificate2' => 'ENDORSEMENT OF CERTIFICATE # 2',
                'gmdss_certificate_endorsement' => 'GMDSS CERTIFICATE/ENDORSEMENT',
                'basic-safety_training' => 'BASIC SAFETY TRAINING',
                'proficiency_in_survival_craft' => 'PROFICIENCY IN SURVIVAL CRAFT',
                'advanced_fire_fighting' => 'ADVANCED FIRE FIGHTING',
                'medical_first_aid' => 'MEDICAL FIRST AID',
                'medical_care' => 'MEDICAL CARE',
                'ships_security_officer' => 'SHIPS SECURITY OFFICER',
                'designated_security_duties' => 'DESIGNATED SECURITY DUTIES',
                'security_awareness' => 'SECURITY AWARENESS',
                'ships_safety_officer_ism' => 'SHIPS SAFETY OFFICER / ISM',
                'radar_navigation' => 'RADAR NAVIGATION, RADAR PLOTTING AND USE OF ARPA',
                'dangerous_cergoes' => 'DANGEROUS & HAZARDOUS CARGOES',
                'bridge_ream_mingt' => 'BRIDGE TEAM MNGT',
                'engine_room_resource_mngt' => 'ENGINE ROOM RESOURCE MNGT',
                'ecdis_generic' => 'ECDIS GENERIC',
                'ecdis_specific' => 'ECDIS SPECIFIC',
                'basic_training_for_oil' => 'BASIC TRAINING FOR OIL & CHEMICAL TANKER CERTIFICATE',
                'adv_training_for_oil' => 'ADV. TRAINING FOR OIL TANKER CERTIFICATE',
                'adv_training_for_chemical' => 'ADV. TRAINING FOR CHEMICAL TANKER CERTIFICATE',
                'basic_training_for_oil_endorsement' => 'BASIC TRAINING FOR OIL AND CHEMICAL TANKER - ENDORSEMENT',
                'adv_training_for_oil_endorsement' => 'ADV. TRAINING FOR OIL TANKER -ENDORSEMENT',
                'adv_training_for_chemical_endorsement' => 'ADV. TRAINING FOR CHEMICAL TANKER - ENDORSEMENT',
                'basic_adv_training' => 'BASIC/ADV. TRAINING FOR GAS TANKER ENDO',
                'high_voltage' => 'HIGH VOLTAGE EL. EQUIPMENT',
                'cook_certificate' => 'COOK CERTIFICATE',
                'messman' => 'MESSMAN (MLC-2006)',
                'yellow_fever_certificate' => 'YELLOW FEVER CERTIFICATE',
                'covid_19_vaccination_certificate' => 'COVID-19 VACCINATION CERTIFICATE',
            ];

            // foreach ($document_types as $key => $label) {
            //     $document_number = sanitize_text_field($_POST[$key . '_number'] ?? '');
            //     $issued_date = sanitize_text_field($_POST[$key . '_issued_date'] ?? '');
            //     $valid_until = sanitize_text_field($_POST[$key . '_valid_until'] ?? '');
            //     $place = sanitize_text_field($_POST[$key . '_place'] ?? '');

            //     // Only insert if any field is filled
            //     if (!empty($document_number) || !empty($issued_date) || !empty($valid_until) || !empty($place)) {
            //         $document_data = [
            //             'user_id' => $user_id,
            //             'document_type' => $label,
            //             'document_number' => $document_number,
            //             'issued_date' => $issued_date,
            //             'valid_until' => $valid_until,
            //             'place' => $place,
            //         ];
            //         $wpdb->insert($application_table_name, $document_data);
            //     }
            // }
            foreach ($document_types as $key => $label) {
                // Collect data, using empty string if the field is not present
                $document_number = sanitize_text_field($_POST[$key . '_number'] ?? '');
                $issued_date = sanitize_text_field($_POST[$key . '_issued_date'] ?? '');
                $valid_until = sanitize_text_field($_POST[$key . '_valid_until'] ?? '');
                $place = sanitize_text_field($_POST[$key . '_place'] ?? '');
            
                // Insert the data regardless of whether any fields are empty
                $document_data = [
                    'user_id' => $user_id,
                    'document_type' => $label,
                    'document_number' => $document_number,
                    'issued_date' => $issued_date,
                    'valid_until' => $valid_until,
                    'place' => $place,
                ];
            
                // Insert the document data into the database
                $wpdb->insert($application_table_name, $document_data);
            }
            $seaman_records_table = $wpdb->prefix . 'seaman_records';

            if (isset($_POST['flag']) && is_array($_POST['flag'])) {
            foreach ($_POST['flag'] as $index => $flag) {
                $flag_number = sanitize_text_field($_POST['flag_number'][$index]);
                $issued_date = sanitize_text_field($_POST['issued_date'][$index]);
                $valid_until = sanitize_text_field($_POST['valid_until'][$index]);
                $place = sanitize_text_field($_POST['place'][$index]);

                if (!empty(trim($flag)) && !empty(trim($flag_number)) && !empty(trim($issued_date)) && !empty(trim($valid_until)) && !empty(trim($place))) {
                    $flag_data = [
                        'user_id' => $user_id, // Replace with the correct user ID logic
                        'flag' => sanitize_text_field($flag),
                        'flag_number' => sanitize_text_field($flag_number),
                        'issued_date' => sanitize_text_field($issued_date),
                        'valid_until' => sanitize_text_field($valid_until),
                        'place' => sanitize_text_field($place),
                    ];
                    $wpdb->insert($seaman_records_table, $flag_data);
                }
            }
        }

        $previous_sea_service = $wpdb->prefix . 'sea_service';

        // Check if sea service data exists and is an array
        if (isset($_POST['from_date']) && is_array($_POST['from_date'])) {
            foreach ($_POST['from_date'] as $index => $from_date) {
                $to_date = sanitize_text_field($_POST['to_date'][$index]);
                $position = sanitize_text_field($_POST['position'][$index]);
                $salary = sanitize_text_field($_POST['salary'][$index]);
                $name_vessel = sanitize_text_field($_POST['name_vessel'][$index]);
                $ship_owner = sanitize_text_field($_POST['ship_owner'][$index]);
                $type_vessel = sanitize_text_field($_POST['type_vessel'][$index]);
                $type_engine = sanitize_text_field($_POST['type_engine'][$index]);
                $build_year = sanitize_text_field($_POST['build_year'][$index]);
                $dwt = sanitize_text_field($_POST['dwt'][$index]);
                $bhp = sanitize_text_field($_POST['bhp'][$index]);
                $flag = sanitize_text_field($_POST['flag'][$index]);
                $crewing_agent = sanitize_text_field($_POST['crewing_agent'][$index]);
            
                // Insert only if at least one field is filled
                if (!empty($from_date) || !empty($to_date) || !empty($position) || !empty($salary) || 
                    !empty($name_vessel) || !empty($ship_owner) || !empty($type_vessel) || 
                    !empty($type_engine) || !empty($build_year) || !empty($dwt) || 
                    !empty($bhp) || !empty($flag) || !empty($crewing_agent)) {
                        
                    $sea_data = [
                        'user_id' => $user_id, // Replace with appropriate user_id logic
                        'from_date' => sanitize_text_field($from_date),
                        'to_date' => $to_date,
                        'position' => $position,
                        'salary' => $salary,
                        'name_vessel' => $name_vessel,
                        'ship_owner' => $ship_owner,
                        'type_vessel' => $type_vessel,
                        'type_engine' => $type_engine,
                        'build_year' => $build_year,
                        'dwt' => $dwt,
                        'bhp' => $bhp,
                        'flag' => $flag,
                        'crewing_agent' => $crewing_agent,
                    ];
                    $wpdb->insert($previous_sea_service, $sea_data);
                }
            }
        }
            
        $previous_employers = $wpdb->prefix . 'employers_table';
        // Insert employers data into the employers_table
        if (isset($_POST['company']) && is_array($_POST['company'])) {
            foreach ($_POST['company'] as $index => $company) {
                $person_in_charge = sanitize_text_field($_POST['person_in_charge'][$index]);
                $contact_details = sanitize_text_field($_POST['contact_details'][$index]);
    
                    if (!empty($company) || !empty($person_in_charge) || !empty($contact_details)) {
                        $employers_data = [
                            'user_id' => $user_id,
                            'company' => sanitize_text_field($company),
                            'person_in_charge' => $person_in_charge,
                            'contact_details' => $contact_details,
                        ];
                        $wpdb->insert($previous_employers, $employers_data);
                    }
                }
            }
      // Send an email to the user
      $to = $user_email;
      $subject = 'Your Application has been Submitted';
      $message = 'Dear ' . $user_login . ",\n\n";
      $message .= 'Thank you for submitting your application. We will review it shortly.';
      $headers = ['Content-Type: text/plain; charset=UTF-8'];

      wp_mail($to, $subject, $message, $headers);
        // Redirect to the previous page with a success flag
        wp_redirect(add_query_arg('success', 'true', wp_get_referer()));
        exit;
        }
    }
}
}
add_action('init', 'wpmsf_handle_application_submission');
function wpmsf_validate_email() {
    global $wpdb;

    if (!isset($_POST['email'])) {
        wp_send_json_error('No email provided.');
    }

    $email = sanitize_email($_POST['email']);
    $form_data_table = $wpdb->prefix . 'form_data';
    $users_table = $wpdb->prefix . 'users'; // The table where WordPress users are stored

    // Check if the email exists in the form_data table
    $email_exists_in_form_data = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $form_data_table WHERE email = %s",
        $email
    ));

    // Check if the email exists in the wp_users table
    $email_exists_in_wp_users = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $users_table WHERE user_email = %s",
        $email
    ));

    if ($email_exists_in_form_data > 0 || $email_exists_in_wp_users > 0) {
        wp_send_json_error('Email already exists.');
    } else {
        wp_send_json_success('Email is available.');
    }
}
add_action('wp_ajax_validate_email', 'wpmsf_validate_email');
add_action('wp_ajax_nopriv_validate_email', 'wpmsf_validate_email');

function wpmsf_admin_menu() {
    add_menu_page(
        'Form Submissions',
        'Form Submissions',
        'manage_options',
        'form-submissions',
        'wpmsf_display_submissions',
        'dashicons-feedback',
        20
    );
}
add_action('admin_menu', 'wpmsf_admin_menu');
function wpmsf_display_submissions() {
    global $wpdb;

    // Fetch all users from the database
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}form_data");

    echo '<h1>Form Submissions</h1>';

    // Display all users in a table
    echo '<h2>All Users</h2>';
    echo '<table border="1" style="width: 100%; border-collapse: collapse;">';
    echo '<tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
          </tr>';

    foreach ($users as $user) {
        echo "<tr>
                <td>{$user->id}</td>
                <td>{$user->name}</td>
                <td>{$user->email}</td>
                <td>
                    <a href='?page=form-submissions&user_id={$user->id}' style='margin-right: 10px;'>View</a>
                    <button onclick=\"printUserData({$user->id})\">Print</button>
                </td>
              </tr>";
    }
    echo '</table>';

    // Display user details if a user is selected
    if (isset($_GET['user_id']) && intval($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        $form_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}form_data WHERE id = %d", $user_id));
        $documents = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}application_data WHERE user_id = %d",
            $user_id
        ));

        $seaman_records = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seaman_records WHERE user_id = %d",
            $user_id
        ));

        $sea_service = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}sea_service WHERE user_id = %d",
            $user_id
        ));

        $employers = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}employers_table WHERE user_id = %d",
            $user_id
        ));
        if ($form_data) {
            echo '<button onclick="printDetails()">Print User Details</button>';
            echo '<div id="user-details">';
            echo '<h2>User Details</h2>';
            echo '<table>';
            echo '<tr><th>Position applied for:</th><td><input type="text" name="position_applied" value="' . esc_attr($form_data->position_applied) . '" readonly></td>';
            echo '<th>Date of readiness:</th><td><input type="date" name="date_of_readiness" value="' . esc_attr($form_data->date_of_readiness) . '" readonly></td></tr>';

            echo '<tr><th>Surname:</th><td><input type="text" name="surname" value="' . esc_attr($form_data->surname) . '" readonly></td>';
            echo '<th>Name:</th><td><input type="text" name="name" value="' . esc_attr($form_data->name) . '" readonly></td></tr>';

            echo '<tr><th>Father\'s Name:</th><td><input type="text" name="father_name" value="' . esc_attr($form_data->father_name) . '" readonly></td>';
            echo '<th>Mother\'s Name:</th><td><input type="text" name="mother_name" value="' . esc_attr($form_data->mother_name) . '" readonly></td></tr>';

            echo '<tr><th>Date of Birth:</th><td><input type="date" name="date_of_birth" value="' . esc_attr($form_data->date_of_birth) . '" readonly></td>';
            echo '<th>Nationality:</th><td><input type="text" name="nationality" value="' . esc_attr($form_data->nationality) . '" readonly></td></tr>';

            echo '<tr><th>Place of Birth (City, Country):</th><td><input type="text" name="place_of_birth" value="' . esc_attr($form_data->place_of_birth) . '" readonly></td>';
            echo '<th>Marital Status:</th><td><input type="text" name="marital_status" value="' . esc_attr($form_data->marital_status) . '" readonly></td>';
            echo '<th>No. of Children under 18:</th><td><input type="number" name="children_under_18" value="' . esc_attr($form_data->children_under_18) . '" readonly></td></tr>';

            echo '<tr><th>Home Address:</th><td colspan="3"><input type="text" name="home_address" value="' . esc_attr($form_data->home_address) . '" readonly></td></tr>';

            echo '<tr><th>Home Zip:</th><td><input type="text" name="home_zip" value="' . esc_attr($form_data->home_zip) . '" readonly></td>';
            echo '<th>Contact Phone:</th><td><input type="tel" name="contact_phone" value="' . esc_attr($form_data->contact_phone) . '" readonly></td></tr>';

            echo '<tr><th>Email:</th><td><input type="email" name="email" value="' . esc_attr($form_data->email) . '" readonly>';
            echo '<span id="email-validation-message" style="color: red; font-size: 12px;"></span></td>';
            echo '<th>Skype/Telegram:</th><td><input type="text" name="skype_telegram" value="' . esc_attr($form_data->skype_telegram) . '"></td></tr>';

            echo '<tr><th>Next of kin:</th><td><input type="text" name="next_kin" value="' . esc_attr($form_data->next_kin) . '" readonly></td>';
            echo '<th>Relation:</th><td><input type="text" name="relation" value="' . esc_attr($form_data->relation) . '" readonly></td></tr>';

            echo '<tr><th>Next of kin’s address:</th><td><input type="text" name="next_kin_address" value="' . esc_attr($form_data->next_kin_address) . '" readonly></td>';
            echo '<th>Next of kin’s phone No</th><td><input type="tel" name="next_kin_phone" value="' . esc_attr($form_data->next_kin_phone) . '" readonly></td></tr>';

            echo '<tr><th>Height (cm):</th><td><input type="number" name="height" value="' . esc_attr($form_data->height) . '" readonly></td>';
            echo '<th>Weight (kg):</th><td><input type="number" name="weight" value="' . esc_attr($form_data->weight) . '" readonly></td>';
            echo '<th>Size of Overall (EUR):</th><td><input type="text" name="size_overall" value="' . esc_attr($form_data->size_overall) . '" readonly></td></tr>';

            echo '<tr><th>Eye Color:</th><td><input type="text" name="eye_color" value="' . esc_attr($form_data->eye_color) . '" readonly></td>';
            echo '<th>Hair Color:</th><td><input type="text" name="hair_color" value="' . esc_attr($form_data->hair_color) . '" readonly></td>';
            echo '<th>Shoes (EUR):</th><td><input type="text" name="shoes" value="' . esc_attr($form_data->shoes) . '" readonly></td></tr>';
            echo '</table>';

            // Marine Education Table
            echo '<h3>Marine Education</h3>';
            echo '<table>';
            echo '<tr><th>Name of maritime college or academy</th><td><input type="text" name="maritime_college" value="' . esc_attr($form_data->maritime_college) . '" readonly></td>';
            echo '<th>From</th><td><input type="date" name="education_from" value="' . esc_attr($form_data->education_from) . '" readonly></td></tr>';
            echo '<tr><th>Department</th><td><input type="text" name="department" value="' . esc_attr($form_data->department) . '" readonly></td>';
            echo '<th>Till</th><td><input type="date" name="education_till" value="' . esc_attr($form_data->education_till) . '" readonly></td></tr>';
            echo '</table>';
        }
         if ($documents) {
            echo '<h2>Passports and Certificates</h2>';
            echo '<style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                table th, table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                table th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                table tr:hover {
                    background-color: #f1f1f1;
                }
            </style>';
            echo '<table>';
            echo '<tr><th>Document Type</th><th>Number</th><th>Issued Date</th><th>Valid Until</th><th>Place</th></tr>';
            foreach ($documents as $doc) {
                // Check if both document_number and place are not empty
                if (!empty($doc->document_number) && !empty($doc->place)) {
                    echo "<tr>
                            <td>{$doc->document_type}</td>
                            <td>{$doc->document_number}</td>
                            <td>{$doc->issued_date}</td>
                            <td>{$doc->valid_until}</td>
                            <td>{$doc->place}</td>
                          </tr>";
                }
            }
            echo '</table>';
        }
        
        if ($seaman_records) {
            echo '<h2>FOREIGN SEAMAN’S ID / RECORD BOOKS</h2>';
            echo '<table>';
            echo '<tr><th>Flag</th><th>Flag Number</th><th>Issued Date</th><th>Valid Until</th><th>Place</th></tr>';
            foreach ($seaman_records as $seaman) {
                echo "<tr><td>{$seaman->flag}</td><td>{$seaman->flag_number}</td><td>{$seaman->issued_date}</td><td>{$seaman->valid_until}</td><td>{$seaman->place}</td></tr>";
            }
            echo '</table>';
        }

        if ($sea_service) {
            echo '<h2>PREVIOUS SEA SERVICE</h2>';
            echo '<table>';
            echo '<tr><th>From Date</th><th>To Date</th><th>Position</th><th>Salary</th><th>Name Of Vessel</th><th>Ship Owner</th><th>Type Of Vessel</th><th>Type Of Engine</th><th>Build Year</th><th>DWT</th><th>BHP</th><th>Flag</th><th>Crewing Agent</th></tr>';
            foreach ($sea_service as $service) {
                echo "<tr><td>{$service->from_date}</td><td>{$service->to_date}</td><td>{$service->position}</td><td>{$service->salary}</td><td>{$service->name_vessel}</td><td>{$service->ship_owner}</td><td>{$service->type_vessel}</td><td>{$service->type_engine}</td><td>{$service->build_year}</td><td>{$service->dwt}</td><td>{$service->bhp}</td><td>{$service->flag}</td><td>{$service->crewing_agent}</td></tr>";
            }
            echo '</table>';
        }

        if ($employers) {
            echo '<h2>BRIEF INFORMATION ABOUT PREVIOUS EMPLOYERS</h2>';
            echo '<table>';
            echo '<tr><th>Company</th><th>Person in Charge</th><th>CONTACT DETAILS</th></tr>';
            foreach ($employers as $employer) {
                echo "<tr><td>{$employer->company}</td><td>{$employer->person_in_charge}</td><td>{$employer->contact_details}</td></tr>";
            }
            echo '</table>';
             echo '</div>';
        }
    }
    // Add CSS and JavaScript for printing
    echo '<style>
        @media print {
            body * {
                visibility: hidden;
            }
            #user-details, #user-details * {
                visibility: visible;
            }
            #user-details {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>';
    echo '<script>
        function printUserData(userId) {
            window.location.href = "?page=form-submissions&user_id=" + userId;
        }
        function printDetails() {
            window.print();
        }
    </script>';
}


function wpmsf_display_user_records() {
    if (!is_user_logged_in()) {
        return '<p>You need to be logged in to view your records.</p>';
    }

    global $wpdb;
    $user_id = get_current_user_id();

    $form_data = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}form_data WHERE user_id_users = %d", $user_id)
    );

    if (!$form_data) {
        return '<p>No records found for this user.</p>';
    }

    $user_id_from_form_data = $form_data->id;

    $application_data = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}application_data WHERE user_id = %d", $user_id_from_form_data)
    );

    $seaman_records = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}seaman_records WHERE user_id = %d", $user_id_from_form_data)
    );

    $sea_service = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}sea_service WHERE user_id = %d", $user_id_from_form_data)
    );

    $employers = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}employers_table WHERE user_id = %d", $user_id_from_form_data)
    );

    $nonce = wp_create_nonce('update_user_records_nonce');

    ob_start();
    ?>
    <h2>Personal Information</h2>
    <form id="user-records-form">
        <input type="hidden" name="action" value="update_user_records">
        <input type="hidden" name="security" value="<?php echo esc_attr($nonce); ?>">
        <table>
            <tr>
                <th>Position applied for:</th>
                <td>
                <select name="position_applied">
                <option value="- SELECT -" disabled>- Select -</option>
                <?php
                $positions = [
                    "Master",
                    "Chief Engineer",
                    "Chief Officer",
                    "Second Officer",
                    "Second Engineer",
                    "Third Engineer",
                    "Third Officer",
                    "Electrical Engineer",
                    "Fourth Engineer",
                    "Pumpman",
                    "Bosun",
                    "Able Seaman",
                    "Ordinary Seaman",
                    "Deck Cadet",
                    "Motorman",
                    "Motorman Turner",
                    "Wiper",
                    "Fitter",
                    "Cook",
                    "Messman",
                    "Superintendent",
                    "Junior Officer",
                    "Junior Engineer",
                    "ETO Assistant",
                    "Engine Cadet",
                    "Oiler",
                    "Painter",
                    "Electrical Cadet"
                ];

                foreach ($positions as $position) {
                    $selected = ($form_data->position_applied === $position) ? 'selected' : '';
                    echo "<option value=\"" . esc_attr($position) . "\" $selected>" . esc_html($position) . "</option>";
                }
                ?>
            </select>
            <th>Date of readiness:</th>
                <td><input type="date" name="date_of_readiness" value="<?php echo esc_attr($form_data->date_of_readiness); ?>"></td>
            </tr>
            <tr>
                <th>Surname:</th>
                <td><input type="text" name="surname" value="<?php echo esc_attr($form_data->surname); ?>"></td>
                <th>Name:</th>
                <td><input type="text" name="name" value="<?php echo esc_attr($form_data->name); ?>"></td>
            </tr>
            <tr>
                <th>Father's Name:</th>
                <td><input type="text" name="father_name" value="<?php echo esc_attr($form_data->father_name); ?>"></td>
                <th>Mother's Name:</th>
                <td><input type="text" name="mother_name" value="<?php echo esc_attr($form_data->mother_name); ?>"></td>
            </tr>
            <tr>
                <th>Date of Birth:</th>
                <td><input type="date" name="date_of_birth" value="<?php echo esc_attr($form_data->date_of_birth); ?>"></td>
                <th>Nationality:</th>
                    <td>
                        <select name="nationality" required>
                            <option value="" disabled>- Select -</option>
                            <?php
                            $nationalities = [ 
                            "Afghanistan",                   
                            "Algeria",                   
                            "American Samoa",                    
                            "Andorra",                   
                            "Angola",                   
                            "Anguilla",                   
                            "Antarctica",                   
                            "Antigua and Barbuda",                     
                            "Argentina",                   
                            "Armenia",                   
                            "Aruba",                   
                            "Australia",                   
                            "Austria",                   
                            "Azerbaijan",                   
                            "Bahamas (The)",                    
                            "Bahrain",                   
                            "Bangladesh",                   
                            "Barbados",                   
                            "Belarus",                   
                            "Belgium",                   
                            "Belize",                   
                            "Benin",                   
                            "Bermuda",                   
                            "Bhutan",                   
                            "Bolivia (Plurinational State of)",                   
                            "Bonaire, Sint Eustatius and Saba",                   
                            "Bosnia and Herzegovina",                   
                            "Botswana",                   
                            "Bouvet Island",                   
                            "Brazil",                   
                            "British Indian Ocean Territory (the)",                   
                            "Brunei Darussalam",                   
                            "Bulgaria",                   
                            "Burkina Faso",                    
                            "Burundi",                   
                            "Cabo Verde",                    
                            "Cambodia",                   
                            "Cameroon",                   
                            "Canada",                   
                            "Cayman Islands (the)",                   
                            "Central African Republic (the)",                   
                            "Chad",                   
                            "Chile",                   
                            "China",                   
                            "Christmas Island",                    
                            "Cocos (Keeling) Islands (the)",                   
                            "Colombia",                   
                            "Comoros (the)",                   
                            "Congo (the Democratic Republic of the)",                   
                            "Congo (the)",                   
                            "Cook Islands (the)",                   
                            "Costa Rica",                    
                            "Cote d Ivoire",                   
                            "Croatia",                   
                            "Cuba",                   
                            "Curaçao",                   
                            "Cyprus",                   
                            "Czechia",                   
                            "Denmark",                   
                            "Djibouti",                   
                            "Dominica",                   
                            "Dominican Republic (the)",                    
                            "Ecuador",                   
                            "Egypt",                   
                            "El Salvador",                    
                            "Equatorial Guinea",                   
                            "Eritrea",                   
                            "Estonia",                   
                            "Eswatini",                   
                            "Ethiopia",                   
                            "European Union",                 
                            "Falkland Islands (the) [Malvinas]",                   
                            "Faroe Islands (the)",                   
                            "Fiji",                   
                            "Finland",                   
                            "France",                   
                            "French Guiana",                   
                            "French Polynesia",                    
                            "French Southern Territories (the)",                     
                            "Gabon",                   
                            "Gambia (the)",                    
                            "Georgia",                   
                            "Germany",                   
                            "Ghana",                   
                            "Gibraltar",                   
                            "Greece",                   
                            "Greenland",                   
                            "Grenada",                   
                            "Guadeloupe",                   
                            "Guam",                   
                            "Guatemala",                   
                            "Guernsey",                   
                            "Guinea",                   
                            "Guinea-Bissau",                  
                            "Guyana",                   
                            "Haiti",                   
                            "Heard Island and McDonald Islands",                   
                            "Holy See (the)",                   
                            "Honduras",                   
                            "Hong Kong",                    
                            "Hungary",                   
                            "Iceland",                   
                            "India",                   
                            "Indonesia",                   
                            "Iran (Islamic Republic of)",                  
                            "Iraq",                   
                            "Ireland",                   
                            "Isle of Man",                   
                            "Israel",                   
                            "Italy",                   
                            "Jamaica",                   
                            "Japan",                   
                            "Jersey",                   
                            "Jordan",                   
                            "Kazakhstan",                   
                            "Kenya",                   
                            "Kiribati",                   
                            "Korea (the Democratic Peoples Republic of)",                   
                            "Korea (the Republic of)",                   
                            "Kuwait",                   
                            "Kyrgyzstan",                   
                            "Lao Peoples Democratic Republic (the)",                   
                            "Latvia",                   
                            "Lebanon",                   
                            "Lesotho",                   
                            "Liberia",                   
                            "Libya",                   
                            "Liechtenstein",                   
                            "Lithuania",                   
                            "Luxembourg",                   
                            "Macao",                   
                            "Madagascar",                   
                            "Malawi",                   
                            "Malaysia",                   
                            "Maldives",                   
                            "Mali",                   
                            "Malta",                   
                            "Marshall Islands (the)",                    
                            "Martial Island",                   
                            "Martinique",                   
                            "Mauritania",                   
                            "Mauritius",                   
                            "Mayotte",                   
                            "Mexico",                   
                            "Micronesia (Federated States of)",                   
                            "Moldova (the Republic of)",                    
                            "Monaco",                   
                            "Mongolia",                   
                            "Montenegro",                   
                            "Montserrat",                   
                            "Morocco",                   
                            "Mozambique",                   
                            "Myanmar",                   
                            "Namibia",                   
                            "Nauru",                   
                            "Nepal",                   
                            "Netherlands (the)",                   
                            "New Caledonia",                   
                            "New Zealand",                    
                            "Nicaragua",                  
                            "Niger (the)",                    
                            "Nigeria",                   
                            "Niue",                   
                            "Norfolk Island",                   
                            "Northern Mariana Islands (the)",                   
                            "Norway",                   
                            "Oman",                   
                            "Pakistan",                   
                            "Palau",                   
                            "Palestine, State of",                   
                            "Panama",                   
                            "Papua New Guinea",                     
                            "Paraguay",                   
                            "Peru",                   
                            "Philippines (the)",                    
                            "Pitcairn",                   
                            "Poland",                   
                            "Portugal",                   
                            "Puerto Rico",                    
                            "Qatar",                   
                            "Republic of North Macedonia",                   
                            "REUNION ISLAND",                   
                            "Romania",                   
                            "Russian Federation (the)",                  
                            "Rwanda",                   
                            "Saint Barthélemy",                   
                            "Saint Helena, Ascension and Tristan da Cunha",                   
                            "Saint Kitts and Nevis",                  
                            "Saint Lucia",                  
                            "Saint Martin (French part)",                   
                            "Saint Pierre and Miquelon",                  
                            "Saint Vincent and the Grenadines",                   
                            "Samoa",                   
                            "San Marino",                    
                            "Sao Tome and Principe",                  
                            "Saudi Arabia",                    
                            "Senegal",                   
                            "Serbia",                   
                            "Seychelles",                   
                            "Sierra Leone",
                            "Singapore",
                            "Sint Maarten (Dutch part)",
                            "Slovakia",                   
                            "Slovenia",                   
                            "Solomon Islands",                    
                            "Somalia",                   
                            "South Africa",                    
                            "South Georgia and the South Sandwich Islands",
                            "South Sudan",                    
                            "Spain",                   
                            "Sri Lanka",                    
                            "Sudan (the)",                    
                            "Suriname",                   
                            "Svalbard and Jan Mayen",                  
                            "Sweden",                   
                            "Switzerland",                   
                            "Syrian Arab Republic",                   
                            "Taiwan (Province of China)",                   
                            "Tajikistan",                   
                            "Tanzania, United Republic of",                  
                            "Thailand",                   
                            "Timor-Leste",                   
                            "Togo",                   
                            "Tokelau",                   
                            "Tonga",                   
                            "Trinidad and Tobago",                     
                            "Tunisia",                   
                            "Turkey",                   
                            "Turkmenistan",                   
                            "Turks and Caicos Islands (the)",                   
                            "Tuvalu",                   
                            "Uganda",                   
                            "Ukraine",                   
                            "United Arab Emirates (the)",                   
                            "United Kingdom",                   
                            "United States Minor Outlying Islands (the)",                   
                            "United States of America (the)",                   
                            "Uruguay",                   
                            "Uzbekistan",                   
                            "Vanuatu",                   
                            "Venezuela (Bolivarian Republic of)",                   
                            "Viet Nam",                  
                            "Virgin Islands (British)",                   
                            "Virgin Islands (U.S.)",                   
                            "Wallis and Futuna",                   
                            "Western Sahara",                   
                            "Yemen",                   
                            "Zambia",                   
                            "Zimbabwe"      
                            ];
                            foreach ($nationalities as $nationality) {
                                $selected = ($form_data->nationality === $nationality) ? 'selected' : '';
                                echo "<option value=\"" . esc_attr($nationality) . "\" $selected>" . esc_html($nationality) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                        </tr>
            <tr>
                <th>Place of Birth (City, Country):</th>
                <td><input type="text" name="place_of_birth" value="<?php echo esc_attr($form_data->place_of_birth); ?>" ></td>
                <th>Marital Status:</th>
                <td>
                    <select name="marital_status" required>
                        <option value="-SELECT -" disabled>- Select -</option>
                        <?php
                        $marital_status_options = ["Not Married", "Married", "Separated", "Divorced", "Widowed"];
                        foreach ($marital_status_options as $status) {
                            $selected = ($form_data->marital_status === $status) ? 'selected' : '';
                            echo "<option value=\"" . esc_attr($status) . "\" $selected>" . esc_html($status) . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <th>No. of Children under 18:</th>
                <td><input type="number" name="children_under_18" value="<?php echo esc_attr($form_data->children_under_18); ?>" ></td>
            </tr>
            <tr>
                <th>Home Address:</th>
                <td colspan="3"><input type="text" name="home_address" value="<?php echo esc_attr($form_data->home_address); ?>" ></td>
            </tr>
            <tr>
                <th>Home Zip:</th>
                <td><input type="text" name="home_zip" value="<?php echo esc_attr($form_data->home_zip); ?>" ></td>
                <th>Contact Phone:</th>
                <td><input type="tel" name="contact_phone" value="<?php echo esc_attr($form_data->contact_phone); ?>" ></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><input type="email" name="email" value="<?php echo esc_attr($form_data->email); ?>" ></td>
                <th>Skype/Telegram:</th>
                <td><input type="text" name="skype_telegram" value="<?php echo esc_attr($form_data->skype_telegram); ?>" ></td>
            </tr>
            <tr>
                <th>Next of kin:</th>
                <td><input type="text" name="next_kin" value="<?php echo esc_attr($form_data->next_kin); ?>" ></td>
                <th>Relation:</th>
                <td>
                <select name="relation" required>
                    <option value="- Select -" disabled>- Select -</option>
                    <?php
                    $relation_options = ["Son", "Wife", "Daughter", "Mother", "Father", "Friend"];
                    foreach ($relation_options as $relation) {
                        $selected = ($form_data->relation === $relation) ? 'selected' : '';
                        echo "<option value=\"" . esc_attr($relation) . "\" $selected>" . esc_html($relation) . "</option>";
                    }
                    ?>
                </select>
            </td>
            </tr>
            <tr>
                <th>Next of kin's address:</th>
                <td><input type="text" name="next_kin_address" value="<?php echo esc_attr($form_data->next_kin_address); ?>" ></td>
                <th>Next of kin's phone No:</th>
                <td><input type="tel" name="next_kin_phone" value="<?php echo esc_attr($form_data->next_kin_phone); ?>" ></td>
            </tr>
            <tr>
                <th>Height (cm):</th>
                <td><input type="number" name="height" value="<?php echo esc_attr($form_data->height); ?>" ></td>
                <th>Weight (kg):</th>
                <td><input type="number" name="weight" value="<?php echo esc_attr($form_data->weight); ?>" ></td>
                <th>Size of Overall (EUR):</th>
                <td>
                    <select name="size_overall" required>
                        <option value="- SELECT -" disabled>- Select -</option>
                        <?php
                        $size_options = [
                            "XS-UK", "XS-EU", "XS-US", 
                            "S-UK", "S-EU", "S-US", 
                            "M-UK", "M-EU", "M-US", 
                            "L-UK", "L-EU", "L-US", 
                            "XL-UK", "XL-EU", "XL-US", 
                            "XXL-UK", "XXL-EU", "XXL-US"
                        ];
                        foreach ($size_options as $size) {
                            $selected = ($form_data->size_overall === $size) ? 'selected' : '';
                            echo "<option value=\"" . esc_attr($size) . "\" $selected>" . esc_html($size) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Eye Color:</th>
                <td><input type="text" name="eye_color" value="<?php echo esc_attr($form_data->eye_color); ?>" ></td>
                <th>Hair Color:</th>
                <td><input type="text" name="hair_color" value="<?php echo esc_attr($form_data->hair_color); ?>" ></td>
                <th>Shoes (EUR):</th>
                <td><input type="text" name="shoes" value="<?php echo esc_attr($form_data->shoes); ?>" ></td>
            </tr>
        </table>

        <h3>Marine Education</h3>
        <table>
            <tr>
                <th>Name of maritime college or academy</th>
                <td><input type="text" name="maritime_college" value="<?php echo esc_attr($form_data->maritime_college); ?>" ></td>
                <th>From</th>
                <td><input type="date" name="education_from" value="<?php echo esc_attr($form_data->education_from); ?>" ></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><input type="text" name="department" value="<?php echo esc_attr($form_data->department); ?>" ></td>
                <th>Till</th>
                <td><input type="date" name="education_till" value="<?php echo esc_attr($form_data->education_till); ?>" ></td>
            </tr>
        </table>
        <h2>Application Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Document Type</th>
                    <th>Document Number</th>
                    <th>Issued Date</th>
                    <th>Valid Until</th>
                    <th>Place</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($application_data as $app): ?>
                    <tr>
                        <td><input type="text" name="application_data[<?php echo $app->id; ?>][document_type]" value="<?php echo esc_attr($app->document_type); ?>" readonly></td>
                        <td><input type="text" name="application_data[<?php echo $app->id; ?>][document_number]" value="<?php echo esc_attr($app->document_number); ?>"></td>
                        <td><input type="date" name="application_data[<?php echo $app->id; ?>][issued_date]" value="<?php echo esc_attr($app->issued_date); ?>"></td>
                        <td><input type="date" name="application_data[<?php echo $app->id; ?>][valid_until]" value="<?php echo esc_attr($app->valid_until); ?>"></td>
                        <td><input type="text" name="application_data[<?php echo $app->id; ?>][place]" value="<?php echo esc_attr($app->place); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Seaman Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Flag</th>
                    <th>Flag Number</th>
                    <th>Issued Date</th>
                    <th>Valid Until</th>
                    <th>Place</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($seaman_records as $record): ?>
                    <tr>
                    <td>
                    <select name="seaman_records[<?php echo $record->id; ?>][flag]" required>
                        <option value="" disabled>- Select -</option>
                        <?php
                        $flags = [
                            "Afghanistan",                   
                            "Algeria",                   
                            "American Samoa",                    
                            "Andorra",                   
                            "Angola",                   
                            "Anguilla",                   
                            "Antarctica",                   
                            "Antigua and Barbuda",                     
                            "Argentina",                   
                            "Armenia",                   
                            "Aruba",                   
                            "Australia",                   
                            "Austria",                   
                            "Azerbaijan",                   
                            "Bahamas (The)",                    
                            "Bahrain",                   
                            "Bangladesh",                   
                            "Barbados",                   
                            "Belarus",                   
                            "Belgium",                   
                            "Belize",                   
                            "Benin",                   
                            "Bermuda",                   
                            "Bhutan",                   
                            "Bolivia (Plurinational State of)",                   
                            "Bonaire, Sint Eustatius and Saba",                   
                            "Bosnia and Herzegovina",                   
                            "Botswana",                   
                            "Bouvet Island",                   
                            "Brazil",                   
                            "British Indian Ocean Territory (the)",                   
                            "Brunei Darussalam",                   
                            "Bulgaria",                   
                            "Burkina Faso",                    
                            "Burundi",                   
                            "Cabo Verde",                    
                            "Cambodia",                   
                            "Cameroon",                   
                            "Canada",                   
                            "Cayman Islands (the)",                   
                            "Central African Republic (the)",                   
                            "Chad",                   
                            "Chile",                   
                            "China",                   
                            "Christmas Island",                    
                            "Cocos (Keeling) Islands (the)",                   
                            "Colombia",                   
                            "Comoros (the)",                   
                            "Congo (the Democratic Republic of the)",                   
                            "Congo (the)",                   
                            "Cook Islands (the)",                   
                            "Costa Rica",                    
                            "Cote d Ivoire",                   
                            "Croatia",                   
                            "Cuba",                   
                            "Curaçao",                   
                            "Cyprus",                   
                            "Czechia",                   
                            "Denmark",                   
                            "Djibouti",                   
                            "Dominica",                   
                            "Dominican Republic (the)",                    
                            "Ecuador",                   
                            "Egypt",                   
                            "El Salvador",                    
                            "Equatorial Guinea",                   
                            "Eritrea",                   
                            "Estonia",                   
                            "Eswatini",                   
                            "Ethiopia",                   
                            "European Union",                 
                            "Falkland Islands (the) [Malvinas]",                   
                            "Faroe Islands (the)",                   
                            "Fiji",                   
                            "Finland",                   
                            "France",                   
                            "French Guiana",                   
                            "French Polynesia",                    
                            "French Southern Territories (the)",                     
                            "Gabon",                   
                            "Gambia (the)",                    
                            "Georgia",                   
                            "Germany",                   
                            "Ghana",                   
                            "Gibraltar",                   
                            "Greece",                   
                            "Greenland",                   
                            "Grenada",                   
                            "Guadeloupe",                   
                            "Guam",                   
                            "Guatemala",                   
                            "Guernsey",                   
                            "Guinea",                   
                            "Guinea-Bissau",                  
                            "Guyana",                   
                            "Haiti",                   
                            "Heard Island and McDonald Islands",                   
                            "Holy See (the)",                   
                            "Honduras",                   
                            "Hong Kong",                    
                            "Hungary",                   
                            "Iceland",                   
                            "India",                   
                            "Indonesia",                   
                            "Iran (Islamic Republic of)",                  
                            "Iraq",                   
                            "Ireland",                   
                            "Isle of Man",                   
                            "Israel",                   
                            "Italy",                   
                            "Jamaica",                   
                            "Japan",                   
                            "Jersey",                   
                            "Jordan",                   
                            "Kazakhstan",                   
                            "Kenya",                   
                            "Kiribati",                   
                            "Korea (the Democratic Peoples Republic of)",                   
                            "Korea (the Republic of)",                   
                            "Kuwait",                   
                            "Kyrgyzstan",                   
                            "Lao Peoples Democratic Republic (the)",                   
                            "Latvia",                   
                            "Lebanon",                   
                            "Lesotho",                   
                            "Liberia",                   
                            "Libya",                   
                            "Liechtenstein",                   
                            "Lithuania",                   
                            "Luxembourg",                   
                            "Macao",                   
                            "Madagascar",                   
                            "Malawi",                   
                            "Malaysia",                   
                            "Maldives",                   
                            "Mali",                   
                            "Malta",                   
                            "Marshall Islands (the)",                    
                            "Martial Island",                   
                            "Martinique",                   
                            "Mauritania",                   
                            "Mauritius",                   
                            "Mayotte",                   
                            "Mexico",                   
                            "Micronesia (Federated States of)",                   
                            "Moldova (the Republic of)",                    
                            "Monaco",                   
                            "Mongolia",                   
                            "Montenegro",                   
                            "Montserrat",                   
                            "Morocco",                   
                            "Mozambique",                   
                            "Myanmar",                   
                            "Namibia",                   
                            "Nauru",                   
                            "Nepal",                   
                            "Netherlands (the)",                   
                            "New Caledonia",                   
                            "New Zealand",                    
                            "Nicaragua",                  
                            "Niger (the)",                    
                            "Nigeria",                   
                            "Niue",                   
                            "Norfolk Island",                   
                            "Northern Mariana Islands (the)",                   
                            "Norway",                   
                            "Oman",                   
                            "Pakistan",                   
                            "Palau",                   
                            "Palestine, State of",                   
                            "Panama",                   
                            "Papua New Guinea",                     
                            "Paraguay",                   
                            "Peru",                   
                            "Philippines (the)",                    
                            "Pitcairn",                   
                            "Poland",                   
                            "Portugal",                   
                            "Puerto Rico",                    
                            "Qatar",                   
                            "Republic of North Macedonia",                   
                            "REUNION ISLAND",                   
                            "Romania",                   
                            "Russian Federation (the)",                  
                            "Rwanda",                   
                            "Saint Barthélemy",                   
                            "Saint Helena, Ascension and Tristan da Cunha",                   
                            "Saint Kitts and Nevis",                  
                            "Saint Lucia",                  
                            "Saint Martin (French part)",                   
                            "Saint Pierre and Miquelon",                  
                            "Saint Vincent and the Grenadines",                   
                            "Samoa",                   
                            "San Marino",                    
                            "Sao Tome and Principe",                  
                            "Saudi Arabia",                    
                            "Senegal",                   
                            "Serbia",                   
                            "Seychelles",                   
                            "Sierra Leone",
                            "Singapore",
                            "Sint Maarten (Dutch part)",
                            "Slovakia",                   
                            "Slovenia",                   
                            "Solomon Islands",                    
                            "Somalia",                   
                            "South Africa",                    
                            "South Georgia and the South Sandwich Islands",
                            "South Sudan",                    
                            "Spain",                   
                            "Sri Lanka",                    
                            "Sudan (the)",                    
                            "Suriname",                   
                            "Svalbard and Jan Mayen",                  
                            "Sweden",                   
                            "Switzerland",                   
                            "Syrian Arab Republic",                   
                            "Taiwan (Province of China)",                   
                            "Tajikistan",                   
                            "Tanzania, United Republic of",                  
                            "Thailand",                   
                            "Timor-Leste",                   
                            "Togo",                   
                            "Tokelau",                   
                            "Tonga",                   
                            "Trinidad and Tobago",                     
                            "Tunisia",                   
                            "Turkey",                   
                            "Turkmenistan",                   
                            "Turks and Caicos Islands (the)",                   
                            "Tuvalu",                   
                            "Uganda",                   
                            "Ukraine",                   
                            "United Arab Emirates (the)",                   
                            "United Kingdom",                   
                            "United States Minor Outlying Islands (the)",                   
                            "United States of America (the)",                   
                            "Uruguay",                   
                            "Uzbekistan",                   
                            "Vanuatu",                   
                            "Venezuela (Bolivarian Republic of)",                   
                            "Viet Nam",                  
                            "Virgin Islands (British)",                   
                            "Virgin Islands (U.S.)",                   
                            "Wallis and Futuna",                   
                            "Western Sahara",                   
                            "Yemen",                   
                            "Zambia",                   
                            "Zimbabwe"            
                        ];
                        foreach ($flags as $flag) {
                            $selected = ($record->flag === $flag) ? 'selected' : '';
                            echo "<option value=\"" . esc_attr($flag) . "\" $selected>" . esc_html($flag) . "</option>";
                        }
                        ?>
                    </select>
                    </td>
                        <td><input type="text" name="seaman_records[<?php echo $record->id; ?>][flag_number]" value="<?php echo esc_attr($record->flag_number); ?>"></td>
                        <td><input type="date" name="seaman_records[<?php echo $record->id; ?>][issued_date]" value="<?php echo esc_attr($record->issued_date); ?>"></td>
                        <td><input type="date" name="seaman_records[<?php echo $record->id; ?>][valid_until]" value="<?php echo esc_attr($record->valid_until); ?>"></td>
                        <td><input type="text" name="seaman_records[<?php echo $record->id; ?>][place]" value="<?php echo esc_attr($record->place); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

   <?php if ($sea_service) : ?>
        <h2>Previous Sea Service</h2>
        <table>
            <thead>
                <tr>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Name Of Vessel</th>
                    <th>Ship Owner</th>
                    <th>Type Of Vessel</th>
                    <th>Type Of Engine</th>
                    <th>Build Year</th>
                    <th>DWT</th>
                    <th>BHP</th>
                    <th>Flag</th>
                    <th>Crewing Agent</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sea_service as $service): ?>
                <tr>
                    <td><input type="date" name="sea_service[<?php echo $service->id; ?>][from_date]" value="<?php echo esc_attr($service->from_date); ?>"></td>
                    <td><input type="date" name="sea_service[<?php echo $service->id; ?>][to_date]" value="<?php echo esc_attr($service->to_date); ?>"></td>
                    <td>
                        <select name="sea_service[<?php echo $service->id; ?>][position]" required>
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <?php
                            $positions = [
                                "MAST", "CE", "CO", "2O", "2E", "3E", "3O", "EE", "4E", 
                                "PUMP", "BOSUN", "AB", "OS", "DC", "MTM", "MTM TURNER", 
                                "WIPER", "FITTER", "COOK", "MESS", "SUPER", "JO", "JE", 
                                "ETO", "ETO AS", "EC", "OIL", "PAINT", "ELECAD"
                            ];
                            foreach ($positions as $position) {
                                $selected = ($service->position === $position) ? 'selected' : '';
                                echo "<option value=\"" . esc_attr($position) . "\" $selected>" . esc_html($position) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="text" name="sea_service[<?php echo $service->id; ?>][salary]" value="<?php echo esc_attr($service->salary); ?>"></td>
                    <td><input type="text" name="sea_service[<?php echo $service->id; ?>][name_vessel]" value="<?php echo esc_attr($service->name_vessel); ?>"></td>
                    <td><input type="text" name="sea_service[<?php echo $service->id; ?>][ship_owner]" value="<?php echo esc_attr($service->ship_owner); ?>"></td>
                    <td>
                        <select name="sea_service[<?php echo $service->id; ?>][type_vessel]" required>
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <?php
                            $vessel_types = [
                                "PASSENGER", "OTHER", "CONTAINER", "LNG", "BULK CARRIER", 
                                "CABLE SHIP", "GAS", "TANKER", "PURE CAR CARRIER", 
                                "WOOD CHIP", "GENERAL CARGO", "NON CARGO SHIP", 
                                "ROLL ON ROLL OFF", "SPECIALIZED VESSEL", "CHEMICAL", 
                                "OIL", "OIL AND CHEM", "TRAINING SHIP", "HEAVY LIFT", 
                                "OTHER DRY", "CYRIL VESSEL", "REEFER", "TUG", "Survery", "SAIL SHIP"
                            ];
                            foreach ($vessel_types as $vessel_type) {
                                $selected = ($service->type_vessel === $vessel_type) ? 'selected' : '';
                                echo "<option value=\"" . esc_attr($vessel_type) . "\" $selected>" . esc_html($vessel_type) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name="sea_service[<?php echo $service->id; ?>][type_engine]" required>
                            <option value="- SELECT -" selected disabled>- Select -</option>
                            <?php
                            $engine_types = [
                                "MAN Diesel &amp; Turbo", "Wärtsilä", "Caterpillar", "Rolls-Royce",
                                "Kongsberg", "General Electric", "ABB (Azipod)", "Cummins",
                                "Yanmar", "Hyundai", "Daihatsu", "Volvo Penta", "Deutz", 
                                "Doosan", "Mitsubishi", "SKL Diesel", "Baudouin", "Pielstick", 
                                "Scania"
                            ];
                            foreach ($engine_types as $engine_type) {
                                $selected = ($service->type_engine === $engine_type) ? 'selected' : '';
                                echo "<option value=\"" . esc_attr($engine_type) . "\" $selected>" . esc_html($engine_type) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="text" name="sea_service[<?php echo $service->id; ?>][build_year]" value="<?php echo esc_attr($service->build_year); ?>"></td>
                    <td><input type="text" name="sea_service[<?php echo $service->id; ?>][dwt]" value="<?php echo esc_attr($service->dwt); ?>"></td>
                    <td><input type="text" name="sea_service[<?php echo $service->id; ?>][bhp]" value="<?php echo esc_attr($service->bhp); ?>"></td>
                    <td>
                        <select name="sea_service[<?php echo $service->id; ?>][flag]" required>
                            <option value="" disabled>- Select -</option>
                            <?php
                            $flags = [
                            "Afghanistan",                   
                            "Algeria",                   
                            "American Samoa",                    
                            "Andorra",                   
                            "Angola",                   
                            "Anguilla",                   
                            "Antarctica",                   
                            "Antigua and Barbuda",                     
                            "Argentina",                   
                            "Armenia",                   
                            "Aruba",                   
                            "Australia",                   
                            "Austria",                   
                            "Azerbaijan",                   
                            "Bahamas (The)",                    
                            "Bahrain",                   
                            "Bangladesh",                   
                            "Barbados",                   
                            "Belarus",                   
                            "Belgium",                   
                            "Belize",                   
                            "Benin",                   
                            "Bermuda",                   
                            "Bhutan",                   
                            "Bolivia (Plurinational State of)",                   
                            "Bonaire, Sint Eustatius and Saba",                   
                            "Bosnia and Herzegovina",                   
                            "Botswana",                   
                            "Bouvet Island",                   
                            "Brazil",                   
                            "British Indian Ocean Territory (the)",                   
                            "Brunei Darussalam",                   
                            "Bulgaria",                   
                            "Burkina Faso",                    
                            "Burundi",                   
                            "Cabo Verde",                    
                            "Cambodia",                   
                            "Cameroon",                   
                            "Canada",                   
                            "Cayman Islands (the)",                   
                            "Central African Republic (the)",                   
                            "Chad",                   
                            "Chile",                   
                            "China",                   
                            "Christmas Island",                    
                            "Cocos (Keeling) Islands (the)",                   
                            "Colombia",                   
                            "Comoros (the)",                   
                            "Congo (the Democratic Republic of the)",                   
                            "Congo (the)",                   
                            "Cook Islands (the)",                   
                            "Costa Rica",                    
                            "Cote d Ivoire",                   
                            "Croatia",                   
                            "Cuba",                   
                            "Curaçao",                   
                            "Cyprus",                   
                            "Czechia",                   
                            "Denmark",                   
                            "Djibouti",                   
                            "Dominica",                   
                            "Dominican Republic (the)",                    
                            "Ecuador",                   
                            "Egypt",                   
                            "El Salvador",                    
                            "Equatorial Guinea",                   
                            "Eritrea",                   
                            "Estonia",                   
                            "Eswatini",                   
                            "Ethiopia",                   
                            "European Union",                 
                            "Falkland Islands (the) [Malvinas]",                   
                            "Faroe Islands (the)",                   
                            "Fiji",                   
                            "Finland",                   
                            "France",                   
                            "French Guiana",                   
                            "French Polynesia",                    
                            "French Southern Territories (the)",                     
                            "Gabon",                   
                            "Gambia (the)",                    
                            "Georgia",                   
                            "Germany",                   
                            "Ghana",                   
                            "Gibraltar",                   
                            "Greece",                   
                            "Greenland",                   
                            "Grenada",                   
                            "Guadeloupe",                   
                            "Guam",                   
                            "Guatemala",                   
                            "Guernsey",                   
                            "Guinea",                   
                            "Guinea-Bissau",                  
                            "Guyana",                   
                            "Haiti",                   
                            "Heard Island and McDonald Islands",                   
                            "Holy See (the)",                   
                            "Honduras",                   
                            "Hong Kong",                    
                            "Hungary",                   
                            "Iceland",                   
                            "India",                   
                            "Indonesia",                   
                            "Iran (Islamic Republic of)",                  
                            "Iraq",                   
                            "Ireland",                   
                            "Isle of Man",                   
                            "Israel",                   
                            "Italy",                   
                            "Jamaica",                   
                            "Japan",                   
                            "Jersey",                   
                            "Jordan",                   
                            "Kazakhstan",                   
                            "Kenya",                   
                            "Kiribati",                   
                            "Korea (the Democratic Peoples Republic of)",                   
                            "Korea (the Republic of)",                   
                            "Kuwait",                   
                            "Kyrgyzstan",                   
                            "Lao Peoples Democratic Republic (the)",                   
                            "Latvia",                   
                            "Lebanon",                   
                            "Lesotho",                   
                            "Liberia",                   
                            "Libya",                   
                            "Liechtenstein",                   
                            "Lithuania",                   
                            "Luxembourg",                   
                            "Macao",                   
                            "Madagascar",                   
                            "Malawi",                   
                            "Malaysia",                   
                            "Maldives",                   
                            "Mali",                   
                            "Malta",                   
                            "Marshall Islands (the)",                    
                            "Martial Island",                   
                            "Martinique",                   
                            "Mauritania",                   
                            "Mauritius",                   
                            "Mayotte",                   
                            "Mexico",                   
                            "Micronesia (Federated States of)",                   
                            "Moldova (the Republic of)",                    
                            "Monaco",                   
                            "Mongolia",                   
                            "Montenegro",                   
                            "Montserrat",                   
                            "Morocco",                   
                            "Mozambique",                   
                            "Myanmar",                   
                            "Namibia",                   
                            "Nauru",                   
                            "Nepal",                   
                            "Netherlands (the)",                   
                            "New Caledonia",                   
                            "New Zealand",                    
                            "Nicaragua",                  
                            "Niger (the)",                    
                            "Nigeria",                   
                            "Niue",                   
                            "Norfolk Island",                   
                            "Northern Mariana Islands (the)",                   
                            "Norway",                   
                            "Oman",                   
                            "Pakistan",                   
                            "Palau",                   
                            "Palestine, State of",                   
                            "Panama",                   
                            "Papua New Guinea",                     
                            "Paraguay",                   
                            "Peru",                   
                            "Philippines (the)",                    
                            "Pitcairn",                   
                            "Poland",                   
                            "Portugal",                   
                            "Puerto Rico",                    
                            "Qatar",                   
                            "Republic of North Macedonia",                   
                            "REUNION ISLAND",                   
                            "Romania",                   
                            "Russian Federation (the)",                  
                            "Rwanda",                   
                            "Saint Barthélemy",                   
                            "Saint Helena, Ascension and Tristan da Cunha",                   
                            "Saint Kitts and Nevis",                  
                            "Saint Lucia",                  
                            "Saint Martin (French part)",                   
                            "Saint Pierre and Miquelon",                  
                            "Saint Vincent and the Grenadines",                   
                            "Samoa",                   
                            "San Marino",                    
                            "Sao Tome and Principe",                  
                            "Saudi Arabia",                    
                            "Senegal",                   
                            "Serbia",                   
                            "Seychelles",                   
                            "Sierra Leone",
                            "Singapore",
                            "Sint Maarten (Dutch part)",
                            "Slovakia",                   
                            "Slovenia",                   
                            "Solomon Islands",                    
                            "Somalia",                   
                            "South Africa",                    
                            "South Georgia and the South Sandwich Islands",
                            "South Sudan",                    
                            "Spain",                   
                            "Sri Lanka",                    
                            "Sudan (the)",                    
                            "Suriname",                   
                            "Svalbard and Jan Mayen",                  
                            "Sweden",                   
                            "Switzerland",                   
                            "Syrian Arab Republic",                   
                            "Taiwan (Province of China)",                   
                            "Tajikistan",                   
                            "Tanzania, United Republic of",                  
                            "Thailand",                   
                            "Timor-Leste",                   
                            "Togo",                   
                            "Tokelau",                   
                            "Tonga",                   
                            "Trinidad and Tobago",                     
                            "Tunisia",                   
                            "Turkey",                   
                            "Turkmenistan",                   
                            "Turks and Caicos Islands (the)",                   
                            "Tuvalu",                   
                            "Uganda",                   
                            "Ukraine",                   
                            "United Arab Emirates (the)",                   
                            "United Kingdom",                   
                            "United States Minor Outlying Islands (the)",                   
                            "United States of America (the)",                   
                            "Uruguay",                   
                            "Uzbekistan",                   
                            "Vanuatu",                   
                            "Venezuela (Bolivarian Republic of)",                   
                            "Viet Nam",                  
                            "Virgin Islands (British)",                   
                            "Virgin Islands (U.S.)",                   
                            "Wallis and Futuna",                   
                            "Western Sahara",                   
                            "Yemen",                   
                            "Zambia",                   
                            "Zimbabwe"           
                            ];
                        foreach ($flags as $flag) {
                            $selected = ($service->flag === $flag) ? 'selected' : '';
                            echo "<option value=\"" . esc_attr($flag) . "\" $selected>" . esc_html($flag) . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td><input type="text" name="sea_service[<?php echo $service->id; ?>][crewing_agent]" value="<?php echo esc_attr($service->crewing_agent); ?>"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No sea service records found.</p>
    <?php endif; ?>
    <h2>Previous Employers</h2>
    <table>
        <thead>
            <tr>
                <th>Company</th>
                <th>Person In Charge</th>
                <th>Contact Details</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($employers as $employer): ?>
                <tr>
                    <td><input type="text" name="employers[<?php echo $employer->id; ?>][company]" value="<?php echo esc_attr($employer->company); ?>"></td>
                    <td><input type="text" name="employers[<?php echo $employer->id; ?>][person_in_charge]" value="<?php echo esc_attr($employer->person_in_charge); ?>"></td>
                    <td><input type="text" name="employers[<?php echo $employer->id; ?>][contact_details]" value="<?php echo esc_attr($employer->contact_details); ?>"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        <button type="button" id="update-user-records">Update Records</button>
    </form>
    <div id="user-records-message" style="display:none;"></div>

    <script>
       jQuery(document).ready(function($) {

            $('#update-user-records').on('click', function() {
                var formData = $('#user-records-form').serialize();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#user-records-message').show().html('<p>' + response.message + '</p>').css('color', response.success ? 'green' : 'red');
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('user_records', 'wpmsf_display_user_records');

// AJAX handler
function wpmsf_update_user_records() {
    check_ajax_referer('update_user_records_nonce', 'security');

    global $wpdb;
    $user_id = get_current_user_id();

    $form_data_to_update = [
        'position_applied'     => sanitize_text_field($_POST['position_applied']),
        'date_of_readiness'    => sanitize_text_field($_POST['date_of_readiness']),
        'surname'              => sanitize_text_field($_POST['surname']),
        'name'                 => sanitize_text_field($_POST['name']),
        'father_name'          => sanitize_text_field($_POST['father_name']),
        'mother_name'          => sanitize_text_field($_POST['mother_name']),
        'date_of_birth'        => sanitize_text_field($_POST['date_of_birth']),
        'nationality'          => sanitize_text_field($_POST['nationality']),
        'place_of_birth'       => sanitize_text_field($_POST['place_of_birth']),
        'marital_status'       => sanitize_text_field($_POST['marital_status']),
        'children_under_18'    => sanitize_text_field($_POST['children_under_18']),
        'home_address'         => sanitize_text_field($_POST['home_address']),
        'home_zip'             => sanitize_text_field($_POST['home_zip']),
        'contact_phone'        => sanitize_text_field($_POST['contact_phone']),
        'email'                => sanitize_email($_POST['email']),
        //'password'             => sanitize_text_field($_POST['password']),
        'skype_telegram'       => sanitize_text_field($_POST['skype_telegram']),
        'next_kin'             => sanitize_text_field($_POST['next_kin']),
        'relation'             => sanitize_text_field($_POST['relation']),
        'next_kin_address'     => sanitize_text_field($_POST['next_kin_address']),
        'next_kin_phone'       => sanitize_text_field($_POST['next_kin_phone']),
        'height'               => sanitize_text_field($_POST['height']),
        'weight'               => sanitize_text_field($_POST['weight']),
        'size_overall'         => sanitize_text_field($_POST['size_overall']),
        'eye_color'            => sanitize_text_field($_POST['eye_color']),
        'hair_color'           => sanitize_text_field($_POST['hair_color']),
        'shoes'                => sanitize_text_field($_POST['shoes']),
        'maritime_college'     => sanitize_text_field($_POST['maritime_college']),
        'department'           => sanitize_text_field($_POST['department']),
        'education_from'       => sanitize_text_field($_POST['education_from']),
        'education_till'       => sanitize_text_field($_POST['education_till']),
    ];

    $updated_form_data = $wpdb->update(
        "{$wpdb->prefix}form_data",
        $form_data_to_update,
        ['user_id_users' => $user_id]
    );

    // Update wp_users table
    $updated_email = sanitize_email($_POST['email']);
    $updated_name  = sanitize_text_field($_POST['name']);
    //$updated_password = sanitize_text_field($_POST['password']);
    //$hashed_password = wp_hash_password($updated_password);

    $wpdb->update(
        "{$wpdb->users}",
        [
            'user_email' => $updated_email,
            'display_name' => $updated_name,
            'user_login' => $updated_email,
            'user_nicename' => $updated_name,  
           // 'user_pass' => $hashed_password,
            'user_registered' => current_time('mysql'),
        ],
        ['ID' => $user_id]
    );
    if (isset($_POST['application_data'])) {
        foreach ($_POST['application_data'] as $id => $data) {
            $wpdb->update(
                "{$wpdb->prefix}application_data",
                [
                    'document_type'   => sanitize_text_field($data['document_type']),
                    'document_number' => sanitize_text_field($data['document_number']),
                    'issued_date'     => sanitize_text_field($data['issued_date']),
                    'valid_until'     => sanitize_text_field($data['valid_until']),
                    'place'           => sanitize_text_field($data['place']),
                ],
                ['id' => $id]
            );
        }
    }
    // Update seaman_records
    if (isset($_POST['seaman_records'])) {
        foreach ($_POST['seaman_records'] as $id => $data) {
            $wpdb->update(
                "{$wpdb->prefix}seaman_records",
                [
                    'flag' => sanitize_text_field($data['flag']),
                    'flag_number' => sanitize_text_field($data['flag_number']),
                    'issued_date'   => sanitize_text_field($data['issued_date']),
                    'valid_until'        => sanitize_text_field($data['valid_until']),
                    'place'         => sanitize_text_field($data['place']),
                ],
                ['id' => $id]
            );
        }
    }

    // Update sea_service
    if (isset($_POST['sea_service'])) {
        foreach ($_POST['sea_service'] as $id => $data) {
            $wpdb->update(
                "{$wpdb->prefix}sea_service",
                [
                    'from_date'     => sanitize_text_field($data['from_date']),
                    'to_date'       => sanitize_text_field($data['to_date']),
                    'position'       => sanitize_text_field($data['position']),
                    'salary'       => sanitize_text_field($data['salary']),
                    'name_vessel'    => sanitize_text_field($data['name_vessel']),
                    'ship_owner'       => sanitize_text_field($data['ship_owner']),
                    'type_vessel'     => sanitize_text_field($data['type_vessel']),
                    'type_engine'       => sanitize_text_field($data['type_engine']),
                    'build_year'        => sanitize_text_field($data['build_year']),
                    'dwt'       => sanitize_text_field($data['dwt']),
                    'bhp'     => sanitize_text_field($data['bhp']),
                    'flag'       => sanitize_text_field($data['flag']),
                    'crewing_agent'        => sanitize_text_field($data['crewing_agent']),
                ],
                ['id' => $id]
            );
        }
    }

    // Update employers
    if (isset($_POST['employers'])) {
        foreach ($_POST['employers'] as $id => $data) {
            $wpdb->update(
                "{$wpdb->prefix}employers_table",
                [
                    'company' => sanitize_text_field($data['company']),
                    'person_in_charge'      => sanitize_text_field($data['person_in_charge']),
                    'contact_details'    => sanitize_text_field($data['contact_details']),
                ],
                ['id' => $id]
            );
        }
    }
    wp_send_json([
        'success' => true,
        'message' => 'Records updated successfully.'
    ]);
}
add_action('wp_ajax_update_user_records', 'wpmsf_update_user_records');