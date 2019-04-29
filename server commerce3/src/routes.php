<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

// Render Twig template in route
// homepage
$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'home.html');
})->setName('home');

// signup get
$app->get('/signup', function ($request, $response, $args) {
    // CSRF token name and value
    $csrfNameKey = $this->csrf->getTokenNameKey();
    $csrfValueKey = $this->csrf->getTokenValueKey();
    $csrfName = $this->csrf->getTokenName();
    $csrfValue = $this->csrf->getTokenValue();

    $csrfArray = [
        'keys' => [
            'name'  => $csrfNameKey,
            'value' => $csrfValueKey
        ],
        'name'  => $csrfName,
        'value' => $csrfValue
    ];

    return $this->view->render($response, 'signup.html', [
        'csrf' => $csrfArray
    ]);
})->setName('signup');

//signup post
$app->post('/signup', function (Request $request, Response $response, $args) {
    $signupFormData = $request->getParsedBody();
    $username = $signupFormData['username'];
    $firstname = $signupFormData['firstname'];
    $lastname = $signupFormData['firstname'];
    $password = $signupFormData['password'];

    $stmtCheckForUser = $this->db->prepare("SELECT * FROM users WHERE username = :Username");
    $stmtCheckForUser->bindValue(':Username', $username, PDO::PARAM_STR);
    $stmtCheckForUser->execute();
    
    if ($stmtCheckForUser->rowCount() > 0) {
        //there is already a username so return an error
        return $this->view->render($response, 'signuperror.html');
    } 

    //continue
    $stmt = $this->db->prepare("INSERT INTO users (username, firstname, lastname, passwordhash)
    VALUES (:username, :firstname, :lastname, :passwordhash)");
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);

    //check the two passwords are the same before hashing&salting
    $passwordRetype = $signupFormData['passwordRetype'];
    if ($password !== $passwordRetype) {
        return $this->view->render($response, 'signuperror.html');
    }
    //now continue hashing&salting
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bindValue(':passwordhash', $passwordHash, PDO::PARAM_STR);

    $stmt->execute();
    return $this->view->render($response, 'signupsuccess.html', [
        'session' => $_SESSION
    ]);
});

// login get
$app->get('/login', function ($request, $response, $args) {
    // CSRF token name and value
    $csrfNameKey = $this->csrf->getTokenNameKey();
    $csrfValueKey = $this->csrf->getTokenValueKey();
    $csrfName = $this->csrf->getTokenName();
    $csrfValue = $this->csrf->getTokenValue();

    $csrfArray = [
        'keys' => [
            'name'  => $csrfNameKey,
            'value' => $csrfValueKey
        ],
        'name'  => $csrfName,
        'value' => $csrfValue
    ];

    return $this->view->render($response, 'login.html', [
        'csrf' => $csrfArray
    ]);
})->setName('login');

//login post
$app->post('/login', function (Request $request, Response $response, $args) {
    $loginFormData = $request->getParsedBody();

    $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :Username");
    $stmt->bindValue(':Username', $loginFormData['username'], PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $password = $loginFormData['password'];
    $passwordHash = $user['passwordhash'];

    if (!password_verify($password, $passwordHash)) {
        return $this->view->render($response, 'loginfail.html');
    } else {
        $_SESSION['UserID'] = $user['userid'];
        $_SESSION['Username'] = $user['username'];
        $_SESSION['FirstName'] = $user['firstname'];
        $_SESSION['LastName'] = $user['lastname'];
        $_SESSION['loggedIn'] = 'yes';
        return $this->view->render($response, 'loginsuccess.html', [
            'session' => $_SESSION
        ]);
    }
});

//logout
$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
    return $this->view->render($response, 'logout.html');
})->setName('logout');

// software
$app->get('/software', function ($request, $response, $args) {
    $userID = $_SESSION['UserID'];

    $stmt = $this->db->prepare("SELECT * FROM educationalsoftware WHERE userid = :UserID");
    $stmt->bindValue(':UserID', $userID, PDO::PARAM_INT);
    $stmt->execute();

	if ($stmt->rowCount() > 0) {
        $softwareData = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$softwareExists = 'true';
    } else {
		$softwareExists = 'false';
	}
	
    return $this->view->render($response, 'software.html', [
        'session' => $_SESSION,
		'softwareExists' => $softwareExists,
        'userSoftware' => $softwareData
    ]);
})->setName('software');

// inputsoftware get
$app->get('/inputsoftware', function ($request, $response, $args) {
    // CSRF token name and value
    $csrfNameKey = $this->csrf->getTokenNameKey();
    $csrfValueKey = $this->csrf->getTokenValueKey();
    $csrfName = $this->csrf->getTokenName();
    $csrfValue = $this->csrf->getTokenValue();

    $csrfArray = [
        'keys' => [
            'name'  => $csrfNameKey,
            'value' => $csrfValueKey
        ],
        'name'  => $csrfName,
        'value' => $csrfValue
    ];

    return $this->view->render($response, 'inputsoftware.html', [
        'session' => $_SESSION,
        'csrf' => $csrfArray
    ]);
})->setName('inputsoftware');

// inputsoftware post
$app->post('/inputsoftware', function (Request $request, Response $response, $args) {
    $softwareFormData = $request->getParsedBody();
    $softwareName = $softwareFormData['inputSoftwareTitle'];
    $softwareVendor = $softwareFormData['inputSoftwareVendor'];
    $softwareNotes = $softwareFormData['inputNotes'];
    $userID = $_SESSION['UserID'];

    $stmtCheckForSoftware = $this->db->prepare("SELECT * FROM educationalsoftware WHERE nameofsoftware = :SoftwareName");
    $stmtCheckForSoftware->bindValue(':SoftwareName', $softwareName, PDO::PARAM_STR);
    $stmtCheckForSoftware->execute();
    
    if ($stmtCheckForSoftware->rowCount() > 0) {
        //there is already educational software that has notes so return an error
        return $this->view->render($response, 'inputsoftwareerror.html');
    }

    //continue
    $stmt = $this->db->prepare("INSERT INTO educationalsoftware (userid, nameofsoftware, softwarevendor, notesonsoftware)
    VALUES (:UserID, :NameOfSoftware, :SoftwareVendor, :NotesOnSoftware)");

    $stmt->bindValue(':UserID', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':NameOfSoftware', $softwareName, PDO::PARAM_STR);
    $stmt->bindValue(':SoftwareVendor', $softwareVendor, PDO::PARAM_STR);
    $stmt->bindValue(':NotesOnSoftware', $softwareNotes, PDO::PARAM_STR);

    $stmt->execute();

    return $this->view->render($response, 'inputsoftwaresuccess.html', [
        'session' => $_SESSION
    ]);
});

// inputstudentdata get
$app->get('/inputstudentdata', function ($request, $response, $args) {
    // CSRF token name and value
    $csrfNameKey = $this->csrf->getTokenNameKey();
    $csrfValueKey = $this->csrf->getTokenValueKey();
    $csrfName = $this->csrf->getTokenName();
    $csrfValue = $this->csrf->getTokenValue();

    $csrfArray = [
        'keys' => [
            'name'  => $csrfNameKey,
            'value' => $csrfValueKey
        ],
        'name'  => $csrfName,
        'value' => $csrfValue
    ];

    $userID = $_SESSION['UserID'];
    $stmt = $this->db->prepare("SELECT * FROM educationalsoftware WHERE userid = :UserID");
    $stmt->bindValue(':UserID', $userID, PDO::PARAM_INT);
    $stmt->execute();

    $softwareData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $this->view->render($response, 'inputstudentdata.html', [
        'session' => $_SESSION,
        'csrf' => $csrfArray,
        'userSoftware' => $softwareData
    ]);
})->setName('inputstudentdata');

//input student data post
$app->post('/inputstudentdata', function (Request $request, Response $response, $args) {
    $studentFormData = $request->getParsedBody();
    $studentFName = $studentFormData['inputStudentFName'];
    $studentLName = $studentFormData['inputStudentLName'];
    $softwareID = $studentFormData['inputSoftwareUsed'];
    $testType = $studentFormData['inputTestType'];
    $testScore = $studentFormData['inputTestScore'];
    $studentComments = $studentFormData['inputStudentComments'];
    $userID = $_SESSION['UserID'];

    $stmt = $this->db->prepare("INSERT INTO studentresults (softwareid, userid, studentfname, studentlname, testtype, testscore, studentcomments)
    VALUES (:SoftwareID, :UserID, :StudentFName, :StudentLName, :TestType, :TestScore, :StudentComments)");
    $stmt->bindValue('SoftwareID', $softwareID, PDO::PARAM_INT);
    $stmt->bindValue('UserID', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':StudentFName', $studentFName, PDO::PARAM_STR);
    $stmt->bindValue(':StudentLName', $studentLName, PDO::PARAM_STR);
    $stmt->bindValue(':TestType', $testType, PDO::PARAM_STR);
    $stmt->bindValue(':TestScore', $testScore, PDO::PARAM_STR);
    $stmt->bindValue(':StudentComments', $studentComments, PDO::PARAM_STR);

    $stmt->execute();
    
    return $this->view->render($response, 'inputstudentdatasuccess.html', [
        'session' => $_SESSION
    ]);
});

// view get
$app->get('/view', function ($request, $response, $args) {
    // CSRF token name and value
    $csrfNameKey = $this->csrf->getTokenNameKey();
    $csrfValueKey = $this->csrf->getTokenValueKey();
    $csrfName = $this->csrf->getTokenName();
    $csrfValue = $this->csrf->getTokenValue();

    $csrfArray = [
        'keys' => [
            'name'  => $csrfNameKey,
            'value' => $csrfValueKey
        ],
        'name'  => $csrfName,
        'value' => $csrfValue
    ];

    $userID = $_SESSION['UserID'];
    $stmt = $this->db->prepare("SELECT * FROM educationalsoftware WHERE userid = :UserID");
    $stmt->bindValue(':UserID', $userID, PDO::PARAM_INT);
    $stmt->execute();

    $allSoftwareData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $this->view->render($response, 'view.html', [
        'session' => $_SESSION,
        'csrf' => $csrfArray,
        'allUserSoftware' => $allSoftwareData
    ]);
})->setName('view');

//view post
$app->post('/view', function (Request $request, Response $response, $args) {
    //hack to repopulate select software form upon HTTP POST
    $userID = $_SESSION['UserID'];
    $stmtRepopulateForm = $this->db->prepare("SELECT * FROM educationalsoftware WHERE userid = :UserID");
    $stmtRepopulateForm->bindValue(':UserID', $userID, PDO::PARAM_INT);
    $stmtRepopulateForm->execute();

    $allSoftwareData = $stmtRepopulateForm->fetchAll(PDO::FETCH_ASSOC);

    //now process the form
    $viewFormData = $request->getParsedBody();
    $softwareID = $viewFormData['inputSoftwareUsed'];

    $stmtProcessForm = $this->db->prepare("SELECT * FROM educationalsoftware WHERE softwareid = :SoftwareIDProcessForm");
    $stmtProcessForm->bindValue(':SoftwareIDProcessForm', $softwareID, PDO::PARAM_INT);
    $stmtProcessForm->execute();

    $softwareData = $stmtProcessForm->fetchAll(PDO::FETCH_ASSOC);

    //fetch associated student data regarding software chosen
    $stmt = $this->db->prepare("SELECT * FROM studentresults WHERE softwareid = :SoftwareID");
    $stmt->bindValue(':SoftwareID', $softwareID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $isStudentData = 'true';
        $softwareStudentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $isStudentData = 'false';
        $softwareStudentData = "";
    }
    
    return $this->view->render($response, 'view.html', [
        'session' => $_SESSION,
        'allUserSoftware' => $allSoftwareData,
        'softwareData' => $softwareData[0],
        'sentSoftwareData' => 'true',
        'isStudentData' => $isStudentData,
        'softwareStudentData' => $softwareStudentData
    ]);
});
