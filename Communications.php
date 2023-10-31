<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Communications extends AdminController
{
    public function __construct()
    {
        parent::__construct();        
		$this->load->library('session');
		$this->load->model('CommunicationsModel');
		$this->load->library('form_validation');
    }


	
    public function dashboard()	
	{				
		$data['emails'] = $this->CommunicationsModel->getClientsEmail();
		$data['admin_emails'] = $this->CommunicationsModel->getAdminsEmail();
		
		// Call a function in your model to get the unread message count
        $unread_count = $this->CommunicationsModel->getUnreadMessageCount();		
		// Pass the count to a view
        $data['unread_count'] = $unread_count;
		
		$session_email = $this->session->userdata('session_email');
		// Call a function in your model to get the sent message count
        $sent_count = $this->CommunicationsModel->getSentMessageCount($session_email);		
		// Pass the count to a view
        $data['sent_count'] = $sent_count;

				
		$session_email = $this->session->userdata('session_email');
		$data['cc_admin_emails'] = $this->CommunicationsModel->getAdminsEmailWithOutSessionEmail($session_email);		
				
		$tab = $this->input->get('tab');
				
        switch ($tab) 
		{
			case 'inbox':
                $messages = $this->CommunicationsModel->getInboxCommunicationsWithoutStatus('sent');
                break;
            case 'sent':
                $messages = $this->CommunicationsModel->getCommunicationsByStatus('sent');
                break;
            case 'draft':
                $messages = $this->CommunicationsModel->getCommunicationsByStatus('draft');
                break;
            default:
                // If the 'status' parameter is not provided or doesn't match any known status, retrieve all messages that are not drafts or sent
                $messages = $this->CommunicationsModel->getInboxCommunicationsWithoutStatus();
                break;
        }
        
        $data['messages'] = $messages;
        $this->load->view('admin/communications/dashboard', $data);
		
    }
	
	
	public function compose()	
	{
		$data['emails'] = $this->CommunicationsModel->getClientsEmail();
		$data['admin_emails'] = $this->CommunicationsModel->getAdminsEmail();
		
		// Call a function in your model to get the unread message count
        $unread_count = $this->CommunicationsModel->getUnreadMessageCount();		
		// Pass the count to a view
        $data['unread_count'] = $unread_count;
		
		$session_email = $this->session->userdata('session_email');
		// Call a function in your model to get the sent message count
        $sent_count = $this->CommunicationsModel->getSentMessageCount($session_email);		
		// Pass the count to a view
        $data['sent_count'] = $sent_count;

				
		$session_email = $this->session->userdata('session_email');
		$data['cc_admin_emails'] = $this->CommunicationsModel->getAdminsEmailWithOutSessionEmail($session_email);		
				
		
        
        $data['messages'] = $messages;
        $this->load->view('admin/communications/compose', $data);
	}
	
	
	
	
	public function sendMessage() 
	{
		$this->load->library('email');
		
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'mail.mdsengg.com',
			'smtp_port' => 465,
			'smtp_user' => 'protrack@mdsengg.com',
			'smtp_pass' => 'Protrack',
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n",
		);
		$this->email->initialize($config);

		$this->email->from('protrack@mdsengg.com', 'ProTrack Sender');
		$this->email->to('cadseat07@gmail.com');
		$this->email->subject('Subject of the Email');
		$this->email->message('This is the email message.');

		if ($this->email->send()) 
		{
			echo 'Email sent successfully';
			
			$imap = imap_open('{mail.mdsengg.com:993/imap/ssl}', 'protrack@mdsengg.com', 'Protrack');

			if ($imap) 
			{
				$mailboxes = imap_list($imap, '{mail.mdsengg.com:993/imap/ssl}', '*');
				$sentMailbox = null;

				foreach ($mailboxes as $mailbox) 
				{
					if (strpos($mailbox, 'Sent') !== false) 
					{
						$sentMailbox = $mailbox;
						break;
					}
				}

				if ($sentMailbox) 
				{
					// Get the sent email data from PHPMailer
					$mailData = $this->email->getSentMIMEMessage();

					// Append the sent email to the "Sent" mailbox
					if (imap_append($imap, $sentMailbox, $mailData)) 
					{
						echo 'Email stored in the "Sent" mailbox.';
					} 
					else 
					{
						echo 'Failed to store email in the "Sent" mailbox.';

						// Check for IMAP errors
						$errors = imap_errors();
						if ($errors) 
						{
							foreach ($errors as $error) 
							{
								echo 'IMAP Error: ' . $error . '<br>';
							}
						}
					}
				} 
				else 
				{
					echo 'Could not find a suitable "Sent" mailbox.';
				}

				// Close the IMAP connection
				imap_close($imap);
			} 
			else 
			{
				echo 'Failed to connect to the IMAP server';
			}
		} 
		else 
		{
			echo 'Email could not be sent. Error: ' . $this->email->print_debugger();
		}
	}


	
	
	
	
	
	
	
	/*
	public function index()
	{	
		$this->load->model('CommunicationsModel');
		$data['emails'] = $this->CommunicationsModel->getClientsEmail();		
		$this->load->view('admin/communications/dashboard', $data);
	}
	*/

	
	/*	
	// application/controllers/Communications.php
	public function insertCommunications()
	{				
		// Get data from the form
        $from_email = $this->input->post('from_email');
        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        // Prepare data for database insertion
        $data = array(
            'from_email' => $from_email,
            'to_email' => $to_email,
            'subject' => $subject,
            'message' => $message
        );

        // Call the model method to insert data
        $this->CommunicationsModel->insertCommunications($data);

        // Redirect or show a success message
	}
	*/

	
	public function sendCommunications()
	{				
		$from_email = $this->input->post('from_email');
		//$from_email_parts = explode(" - ", $from_email);
		
		$to_email = $this->input->post('to_email');
		//$to_email_parts = explode(" - ", $to_email);
		$to_email_string = implode(', ', $to_email);
		
		
		// Retrieve the selected email addresses as an array
		$cc_emails = $this->input->post('cc_emails');
				

		if (empty($cc_emails)) 
		{
			// Handle the case when no CC emails are selected
			$cc_emails = []; // Optional: Set it to an empty array, if needed
		} 
		else 
		{
			// Process the selected CC emails
			// $cc_emails is an array containing the selected emails
		}
		

		// Prepare the selected email addresses as a comma-separated string
		$cc_email_string = implode(', ', $cc_emails);
		
		
			
		// Client data found; insert data into the communications table
		$data = array(			
			//'from_email' => $from_email_parts[0],
			//'sender_name' => $from_email_parts[1],
			'from_email' => trim($from_email),
			'to_email' => trim($to_email_string),			
			'subject' => $this->input->post('subject'),
			'cc_emails' => trim($cc_email_string), // Comma-separated list of selected emails
			'message' => $this->input->post('message'),
			'is_reply' => FALSE,
		);

		// Debugging: Check the value of $data
		// echo "Data to be inserted: " . print_r($data, true);

		$inserted_id = $this->CommunicationsModel->insertCommunications($data);
		
		// Set the last insert ID as the thread_id for the original message
		$this->db->where('id', $inserted_id);
		$this->db->update(db_prefix() . 'communications', array('thread_id' => $inserted_id));

		if ($inserted_id > 0) 
		{
			// Data inserted successfully
			$response['success'] = true;
			$response['message'] = 'Success : Message sent successfully!';
		} 
		else 
		{
			// Insertion failed
			$response['success'] = false;
			$response['message'] = 'Error : Failed to send message. Please try again.';
		}

		// Send the JSON response
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
	
	
	public function sendReplyCommunications()
	{
		$reply_from_email = $this->input->post('reply_from_email');
		//$reply_from_email_parts = explode(" - ", $reply_from_email);
		
		$reply_to_email = $this->input->post('reply_to_email');
		//$reply_to_email_parts = explode(" - ", $reply_to_email);

		// Client data found; insert data into the communications table
		$data = array(
			'thread_id' => $this->input->post('reply_thread_id'),
			'from_email' => $reply_from_email,
			'to_email' => $reply_to_email,
			'subject' => $this->input->post('reply_subject'),			
			'message' => $this->input->post('reply_message'),
			'is_reply' => TRUE,
			'in_reply_to' => $this->input->post('in_reply_to'),
		);

		$inserted_id = $this->CommunicationsModel->insertReplyCommunications($data);

		if ($inserted_id > 0) 
		{
			// Data inserted successfully
			$response['success'] = true;
			$response['message'] = 'Success : Message sent successfully!';
		} 
		else 
		{
			// Insertion failed
			$response['success'] = false;
			$response['message'] = 'Error : Failed to send message. Please try again.';
		}
		

		// Send the JSON response
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
	
	
	public function sendReplyAllCommunications()
	{
		$reply_from_email = $this->input->post('reply_from_email');
		//$reply_from_email_parts = explode(" - ", $reply_from_email);
		
		$reply_to_email = $this->input->post('reply_to_email');
		//$reply_to_email_parts = explode(" - ", $reply_to_email);

		// Client data found; insert data into the communications table
		$data = array(
			'thread_id' => $this->input->post('reply_thread_id'),
			'from_email' => $reply_from_email,
			'to_email' => $reply_to_email,
			'subject' => $this->input->post('reply_subject'),			
			'message' => $this->input->post('reply_all_message'),
			'is_reply' => TRUE,
			'in_reply_to' => $this->input->post('in_reply_to'),
		);

		$inserted_id = $this->CommunicationsModel->insertReplyCommunications($data);

		if ($inserted_id > 0) 
		{
			// Data inserted successfully
			$response['success'] = true;
			$response['message'] = 'Success : Message sent successfully!';
		} 
		else 
		{
			// Insertion failed
			$response['success'] = false;
			$response['message'] = 'Error : Failed to send message. Please try again.';
		}
		

		// Send the JSON response
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
	
	
	
	
	
	/*
	public function sendCommunications()
	{
		// Form validation passed; insert data into the database
		$to_email = $this->input->post('to_email');

		// Retrieve client's name and ID based on their email
		$client_data = $this->CommunicationsModel->getClientsByEmail($to_email);

		$response = array(); // Initialize a response array

		if ($client_data) 
		{
			// Client data found; insert data into the communications table
			$data = array(
				'from_email' => $this->input->post('from_email'),
				'to_email' => $to_email,
				'client_id' => $client_data['id'],
				'client_name' => $client_data['firstname'] . " " . $client_data['lastname'],
				'subject' => $this->input->post('subject'),
				'message' => $this->input->post('message'),
				'admin_message_status' => 'sent',
				'clients_message_status' => 'inbox',
			);

			$inserted_id = $this->CommunicationsModel->insertCommunications($data);

			if ($inserted_id > 0) 
			{
				// Data inserted successfully
				$response['success'] = true;
				$response['message'] = 'Success : Message sent successfully!';
			} 
			else 
			{
				// Insertion failed
				$response['success'] = false;
				$response['message'] = 'Error : Failed to send message. Please try again.';
			}
		} 
		else 
		{
			// Client data not found for the provided email
			$response['success'] = false;
			$response['message'] = 'Error : Client data not found for the provided email!';
		}

		// Send the JSON response
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}
	*/
	
	
	/*
	public function view($id) 
	{   
		$data['emails'] = $this->CommunicationsModel->getClientsEmail();	
		
		$session_email = $this->session->userdata('session_email');
		$data['cc_admin_emails'] = $this->CommunicationsModel->getAdminsEmailWithOutSessionEmail($session_email);		
				
        // Fetch the communication record by ID
        $communication = $this->CommunicationsModel->getCommunicationById($id);

        if (!$communication) 
		{
            // Handle the case where the communication with the given ID doesn't exist
            //$this->load->view('admin/communications/dashboard', $data);
			
			redirect(admin_url('communications/dashboard'));
        }

        // Load a view to display the communication details
        $data['communication'] = $communication;
        $this->load->view('admin/communications/view', $data);
    }
	*/
	
	
	
	
	public function view($thread_id) 
	{   
		$data['emails'] = $this->CommunicationsModel->getClientsEmail();
		$data['admin_emails'] = $this->CommunicationsModel->getAdminsEmail();
		
		$session_email = $this->session->userdata('session_email');
		$data['cc_admin_emails'] = $this->CommunicationsModel->getAdminsEmailWithOutSessionEmail($session_email);		
		
		$data['original_messages'] = $this->CommunicationsModel->getOriginalMessage($thread_id);
				
        // Fetch the communication record by thread_id
        $communications = $this->CommunicationsModel->getCommunicationByThreadId($thread_id);
		
		

        if (!$communications) 
		{            
			redirect(admin_url('communications/dashboard'));
        }
        
        $data['communications'] = $communications;
        $this->load->view('admin/communications/view', $data);
    }
	
	
	
	
	
	public function draftCommunications()
	{
		// Form validation passed; insert data into the database
		$to_email = $this->input->post('to_email');

		// Retrieve client's name and ID based on their email
		$client_data = $this->CommunicationsModel->getClientsByEmail($to_email);

		$response = array(); // Initialize a response array

		if ($client_data) 
		{
			// Client data found; insert data into the communications table
			$data = array(
				'from_email' => $this->input->post('from_email'),
				'to_email' => $to_email,
				'client_id' => $client_data['id'],
				'client_name' => $client_data['firstname'] . " " . $client_data['lastname'],
				'subject' => $this->input->post('subject'),
				'message' => $this->input->post('message'),
				'status' => 'draft', // Set the status to 'draft'
			);

			$inserted_id = $this->CommunicationsModel->insertCommunications($data);

			if ($inserted_id > 0) 
			{
				// Data inserted successfully
				$response['success'] = true;
				$response['message'] = 'Success : Message draft successfully!';
			} 
			else 
			{
				// Insertion failed
				$response['success'] = false;
				$response['message'] = 'Error : Failed to draft message. Please try again.';
			}
		} 
		else 
		{
			// Client data not found for the provided email
			$response['success'] = false;
			$response['message'] = 'Error : Client data not found for the provided email!';
		}

		// Send the JSON response
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	}



	public function replySendCommunications()
	{			

		$reply_message_id = $this->input->post('reply_message_id');
		$reply_from_email = $this->input->post('reply_from_email');
		$reply_to_email = $this->input->post('reply_to_email');
		$reply_message = $this->input->post('reply_message');

		if (empty($reply_message_id) || empty($reply_from_email) || empty($reply_to_email) || empty($reply_message) ) 
		{
			// Handle missing data, display an error message, or take appropriate action
			// Client data not found for the provided email
			$response['success'] = false;
			$response['message'] = 'Error : Reply data not found!';
		} 
		else 
		{			
			$data = array(
			'communications_id' => $this->input->post('reply_message_id'),
			'sender_id' => $this->input->post('reply_from_email'),
			'recipient_id' => $this->input->post('reply_to_email'),
			'message' => $this->input->post('reply_message'),
			);
			
			// Call the model method to insert data
			$inserted_id = $this->CommunicationsModel->insertReplyCommunications($data);
			
			if ($inserted_id > 0) 
			{
				// Data inserted successfully
				$response['success'] = true;
				$response['message'] = 'Success : Message draft successfully!';
			} 
			else 
			{
				// Insertion failed
				$response['success'] = false;
				$response['message'] = 'Error : Failed to draft message. Please try again.';
			}
			
		}
		
		// Redirect or show a success message
		// Send the JSON response
		$this->output->set_content_type('application/json')->set_output(json_encode($response));
	
		
	}
	
	
		
	
}
