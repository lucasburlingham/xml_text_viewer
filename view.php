<?php


$xml = simplexml_load_file("/home/lburlingham/Documents/xml_text_viewer/sms-20230406183539.xml");

// Array to hold all files that are split in case of error loading XML file (too large) 
// $splitFiles = array();


// class SplitFileIntoMultipleFiles {
// 	public $filename;
// 	public $linesPerFile;

// 	public function __construct($filename, $linesPerFile) {
// 		$this->filename = $filename;
// 	}

// 	public function splitFile($filename, $linesPerFile) {
// 		$this->filename = $filename;
// 		$command = "split -l " . $linesPerFile . " " . " " . $filename . " " . $filename;
// 		exec($command);

// 		$range = range('a', 'z');
// 		foreach ($range as $letter) {
// 			foreach ($range as $secondletter) {
// 				array_push($splitFiles, $filename . $letter . $secondletter);
// 				echo $filename . $letter . $secondletter . "\n";
// 			}
// 		}

// 		foreach ($splitFiles as $file) {
// 			$xml = simplexml_load_file($file);
// 			if ($xml === false) {
// 				throw new Exception("Failed to load XML file from split file.");
// 			}
// 		}

// 	}
// }


// Check if XML file was loaded successfully
// if ($xml === false) {

// 	// Split the file into multiple files
// 	// $split = new SplitFileIntoMultipleFiles();
// 	// $split->splitFile("/home/lburlingham/Documents/xml_text_viewer/sms-20230406183539.xml", 1000);
// 	$xml = simplexml_load_file("/home/lburlingham/Documents/xml_text_viewer/test.xml");

//     throw new Exception("Failed to load XML file. Trying to split the file into multiple files.");

// }
class AddSMSMessage {

    public $contactName;
    public $body;
    public $read;
    public $dateSent;
    public $readableDate;
    public $address;
    public $sub_id;
    public $type;
    public $date;

    public function __construct($contactName, $body, $read, $dateSent, $readableDate, $address, $sub_id, $date) {
        $this->contactName = $contactName;
        $this->body = $body;
        $this->read = $read;
        $this->dateSent = $dateSent;
        $this->readableDate = $readableDate;
        $this->address = $address;
        $this->sub_id = $sub_id;
        $this->type = "sms";
        $this->date = $date;
        $this->addToData(); // Add message to $data array
    }
    public function addToData() {
        global $data;
        $data[] = $this;
    }
}

class AddMMSMessage {

    public $contactName;
    public $body;
    public $read;
    public $dateSent;
    public $readableDate;
    public $address;
    public $dataBody;
    public $sub_id;
    public $type;
    public $date;

    // Constructor to initialize message object

    public function __construct($contactName, $body, $read, $dateSent, $readableDate, $address, $dataBody, $sub_id, $date) {
        $this->contactName = $contactName;
        $this->body = $body;
        $this->read = $read;
        $this->dateSent = $dateSent;
        $this->readableDate = $readableDate;
        $this->address = $address;
        $this->dataBody = $dataBody;
        $this->sub_id = $sub_id;
        $this->type = "mms";
        $this->date = $date;
		$this->addToData(); // Add message to $data array

	}
	public function addToData() {
        global $data;
        $data[] = $this;
    }
}


// Extract the desired attributes from each <sms> element
foreach ($xml->sms as $sms) {

    // Extract the attributes
    $contactName = (string) $sms['contact_name'];
    $body = (string) $sms['body'];
    $read = $sms['read'] == 1 ? True : False;
    $dateSent = (string) $sms['date_sent'];
    $readableDate = (string) $sms['readable_date'];
    $address = (string) $sms['address'];
    $sub_id = $sms['sub_id'] == -1 ? "sender" : "recipient";
    $date = $sms['date'];

    $message = new AddSMSMessage($contactName, $body, $read, $dateSent, $readableDate, $address, $sub_id, $date);
}

foreach ($xml->mms as $mms) {
    $contactName = (string) $mms['contact_name'];
    $body = (string) $mms['subject'];
    $read = $mms['read'] == 1 ? True : False;
    $dateSent = (string) $mms['date_sent'];
    $readableDate = (string) $mms['readable_date'];
    $address = (string) $mms['address'];
    $dataBody = $mms->parts->part['data'];
    $sub_id = $mms->parts->part['sub_id'];
    $date = $mms['date'];

    $message = new AddMMSMessage($contactName, $body, $read, $dateSent, $readableDate, $address, $dataBody, $sub_id, $date);
}

class DisplayMessage {
    public $contactName;
    public $body;
    public $read;
    public $dateSent;
    public $readableDate;
    public $address;
    public $dataBody;
    public $sub_id;
    public $type;
        public $date;

    public function __construct($contactName, $body, $read, $dateSent, $readableDate, $address, $dataBody, $sub_id, $type) {
        $this->contactName = $contactName;
        $this->body = $body;
        $this->read = $read;
        $this->dateSent = $dateSent;
        $this->readableDate = $readableDate;
        $this->address = $address;
        $this->dataBody = $dataBody;
        $this->sub_id = $sub_id;
        $this->type = $type;


        if ($type == "sms") {

            $html = <<<PHP
                <div class="message">
                    <div class="card $sub_id">
                        <div class="card-body">
                            <p class="card-text">$body
                            </p>
                            <span class="text-muted time">$readableDate</span>
                        </div>
                    </div>
                </div>
            PHP;
        } else {
            $html = <<<PHP
					<div class="message">
						<div class="card sender">
							<div class="card-body mms">
								<img src='data:image/jpeg;base64,$dataBody' class="img"/>
								<br>
								<span class="text-muted time">$readableDate</span>
							</div>
						</div>
					</div>
			PHP;
        }
        echo $html;
    }
}

		// get the q parameter from URL
		if (isset($_REQUEST["q"]) && $_REQUEST["q"] != "" && $_REQUEST["q"] != " ") {
			$keyword = $_REQUEST["q"];

			// Decode the HTML-encoded string
			$decodedKeyword = html_entity_decode($keyword);

			// Split the decoded string into an array using spaces as delimiters
			$keywordArray = explode(" ", $decodedKeyword);

			foreach ($keywordArray as $keyword) {
				// Search for the keyword in the $data array
				foreach ($data as $message) {
					if ($message instanceof AddSMSMessage && stripos($message->body, $keyword) !== false) {
						// Found keyword in SMS message
						$result = array(
							'date' => $message->date,
							'body' => $message->body,
							'sub_id' => $message->sub_id
						);
						$results[] = $result;
						print_r(json_encode($results));
					}
				}
			}
		}
// Sort the $data array by date_sent in epoch time format
usort($data, function($a, $b) {
    return $a->date <=> $b->date;
});

$numMessages = count($data);


?>


<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" href="https://unpkg.com/primer/build/build.css">
	<script src="https://kit.fontawesome.com/7f747d4164.js" crossorigin="anonymous"></script>

	<style>
	.number-showing {
		margin-left: 1em;
		color: #f9f9f9;
	}

	.search-form {
		flex-grow: 1;
		position: relative;
	}

	.search-form input {
		width: 100%;
		padding: 8px;
		border: none;
		background-color: #f8f8f8;
		padding-right: 40px;
		/* Add padding-right for search icon */
	}

	/* Search icon */
	.search-icon {
		position: absolute;
		top: 50%;
		right: 8px;
		transform: translateX(-50%);
		color: #fff;
		z-index: 1;
	}

	.navbar {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		z-index: 1000;
		background-color: black;
		padding: 16px;
		margin-bottom: 16px;
		display: flex;
		align-items: center;
	}


	.navbar-brand {
		color: #fff;
		font-weight: bold;
		font-size: 1.6rem;
	}

	.navbar .search-bar {
		position: relative;
		margin-left: auto;
		max-width: 200px;
	}

	.navbar .search-bar input {
		width: 0;
		opacity: 0;
		transition: width 0.2s ease-in-out, opacity 0.2s ease-in-out;
		border: none;
		outline: none;
		background-color: transparent;
		color: #ffffff;
		padding: 0;
		margin: 0;
		padding-left: 30px;
		font-size: 16px;
		height: 30px;
		cursor: pointer;
	}

	.navbar .search-bar input.active {
		width: 200px;
		opacity: 1;
		padding-left: 10px;
	}

	.navbar .search-bar .search-icon {
		position: absolute;
		top: 50%;
		left: 8px;
		transform: translateY(-50%);
		color: #ffffff;
		cursor: pointer;
		transition: transform 0.2s ease-in-out;
	}

	.navbar .search-bar input.active+.search-icon {
		transform: translateX(-10%) translateY(-50%);
		transform: rotateX(100);
	}

	.navbar .search-bar .search-icon:hover .search-icon:active input.active::placeholder {
		color: #ffffff;
		/* Updated color to white */
	}

	.navbar .search-bar .search-icon:focus {
		outline: none;
	}


	.message {
		margin-top: 1em;
		margin-bottom: 1em;
	}

	.card {
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 1em;
		padding: 1em;
		border-color: cornflowerblue;
		border-width: 2px;
		border-style: solid;
		border-radius: 6px;
		background-color: cornflowerblue;
		color: white;
		width: fit-content;
	}

	.card:hover {
		transform: scale(1.01)
	}

	.card-body {
		font-size: 1.2em;
	}

	.time {
		font-style: italic;
		font-weight: normal;
	}

	body {
		background-color: #fffcf0;
	}

	.recipient {
		margin-left: 4em;
		margin-right: auto;
		margin-bottom: 1em;
		padding: 1em;
		border-color: cornflowerblue;
		border-width: 2px;
		border-style: solid;
		border-radius: 6px;
		background-color: cornflowerblue;
		color: white;
		width: fit-content;
	}

	.sender {
		margin-left: auto;
		margin-right: 4em;
		margin-bottom: 1em;
		padding: 1em;
		border-color: cornflowerblue;
		border-width: 2px;
		border-style: solid;
		border-radius: 6px;
		background-color: cornflowerblue;
		color: white;
		width: fit-content;
	}


	.centered {
		text-align: center;
	}

	img {
		height: calc(80vh - 4em);
	}

	.padding {
		height: 4rem;
	}


	/* Modal styles */
	.modal {
		display: none;
		position: fixed;
		z-index: 1;
		padding-top: 100px;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		overflow: auto;
		background-color: rgba(0, 0, 0, 0.9);
	}

	.modal-content {
		margin: auto;
		display: block;
		width: 80%;
		max-width: 700px;
	}

	.modal-content img {
		width: 100%;
		height: auto;
	}

	.close {
		position: absolute;
		top: 15px;
		right: 35px;
		color: #f1f1f1;
		font-size: 40px;
		font-weight: bold;
		transition: 0.3s;
		cursor: pointer;
	}

	.close:hover,
	.close:focus {
		color: #bbb;
		text-decoration: none;
		cursor: pointer;
	}
	</style>


</head>

<body>

	<!-- HTML markup for the modal -->
	<div id="modal" class="modal">
		<span class="close-btn" id="close-btn">&times;</span>
		<img class="modal-content" id="modalImg" src="" />
	</div>



	<nav class="navbar">
		<div class="navbar-brand">SMS Viewer</div>

		<div class="number-showing">Showing <?php echo $numMessages; ?> messages</div>
		<div class="search-bar">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" id="search-form">
				<input type="text" placeholder="Search..." class="search-input" id="search-input" name="q">
				<i class="fa fa-search search-icon" id="search-icon" aria-hidden="true"></i>
			</form>
		</div>
	</nav>
	<script>
	searchIcon = document.querySelector('.search-bar .search-icon')
	searchIcon.addEventListener('click', function() {
		var input = document.querySelector('.search-bar input');
		input.classList.toggle('active');
		if (input.classList.contains('active')) {
			input.focus();
		}
		searchIcon.removeEventListener('click', searchIconClickHandler);
	});

	// var searchIcon = document.getElementById('search-icon');
	var searchInput = document.getElementById('search-input');

	// Add click event listener to search icon
	searchIcon.addEventListener('click', function() {
		// If search input is hidden, show it and focus on it
		if (searchInput.classList.contains('hidden')) {
			searchIcon.classList.remove('fa-search');
			searchIcon.classList.add('fa-times');
			searchInput.classList.remove('hidden');
			searchInput.focus();
		} else { // If search input is visible, submit the form
			document.getElementById('search-form').submit();
		}
	});
	</script>
	<p class="padding"></p>

	<div class="message-container">
		<?php 

$messagesPerPage = $GET_['messagesPerPage'] ?? 50;

// Count total number of messages
$totalMessages = $numMessages;

// Calculate total number of pages
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = ceil($totalMessages / $messagesPerPage);
$offset = ($page - 1) * $messagesPerPage;

// Get current page number from URL parameter
$messages = array_slice($data, $offset, $messagesPerPage);

	
if ($page < 1) {
    $page = 1;
} elseif ($page > $totalPages) {
    $page = $totalPages;
}

foreach ($messages as $message) {
	if(!isset($message->dataBody)) {
		new DisplayMessage($message->contactName, $message->body, $message->read, $message->dateSent, $message->readableDate, $message->address, "", $message->sub_id, $message->type);
	} else {
		new DisplayMessage($message->contactName, $message->body, $message->read, $message->dateSent, $message->readableDate, $message->address, $message->dataBody, $message->sub_id, $message->type);
	}
}

echo '<br>';
echo <<<PHP
	<div class="centered">
	<span
	Page $page of $totalPages <br>
	PHP;
// Display pagination links
if ($page > 1) {
	$previousPage = $page - 1;
	$html = <<<PHP
		<a href="?page=$previousPage" class="btn">Previous</a>
	PHP;
    echo $html;
} else {
	echo '<a class="btn disabled">Previous</a>';
}
if ($page < $totalPages) {
	$nextPage = $page + 1;
	$html = <<<PHP
		<a href="?page=$nextPage" class="btn">Next</a>
	PHP;
    echo $html;
} else {
	echo '<a class="btn disabled">Next</a>';
}
?>
	</div>
	<br>
	</div>
	<script>
	// JavaScript function to load more messages
	function loadMore(page) {
		// Send AJAX request to reload page with new page parameter
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				// Replace current page content with new content
				document.open();
				document.append(xhr.responseText);
				document.close();
				// Scroll to bottom of page
				window.scrollTo(0, document.body.scrollHeight);
			}
		};
		xhr.open("GET", "?page=" + page, true);
		xhr.send();
	}
	</script>

	<script>
	document.addEventListener("DOMContentLoaded", function() {
		// Get all elements with class name "img"
		var images = document.getElementsByClassName("img");
		console.log("Number of images: " + images.length);
		var modal = document.getElementById("modal");
		var modalImg = document.getElementById("modal-img");
		var closeBtn = document.getElementById("close-btn");


		// Loop through all the images and add a click event listener
		for (var i = 0; i < images.length; i++) {
			console.log("Image path: " + images[i].src);
			var src = images[i].src;
			images[i].addEventListener("click", function() {
				// Set the src attribute of the modal image to the clicked image source
				// modalImg.src = images[i].src;
				modalImg.src = src;

				// Display the modal
				modal.style.display = "block";
			});
		}

		// Add a click event listener to the close button
		closeBtn.addEventListener("click", function() {
			// Hide the modal
			modal.style.display = "none";
			modalImg.src = "";
		});

		// Add a click event listener to the modal element to close it when clicked outside the image
		document.addEventListener("click", function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		});
	});
	</script>
</body>

</html>