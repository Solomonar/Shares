<?php


class Users extends Controller
{
    /** @var User $userModel */
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            //sanitize post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_error' => '',
                'email_error' => '',
                'password_error' => '',
                'confirm_password_error' => '',
            ];

            //Validation
            if (empty($data['email'])) {
                $data['email_error'] = 'Please enter email';
            } else {
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_error'] = 'Email already taken';
                }
            }
            if (empty($data['name'])) {
                $data['name_error'] = 'Please enter email';
            }
            if (empty($data['password'])) {
                $data['password_error'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_error'] = 'Password must be at least 6 characters';
            }
            if (empty($data['confirm_password'])) {
                $data['confirm_password_error'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_error'] = 'Password do not match';
                }
            }

            //Make sure errors are empty
            if (empty($data['email_error']) && empty($data['name_error']) && empty($data['password_error']) && empty($data['confirm_password_error'])) {
                //hash password
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

                //register user

                if ($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    redirect('users/login');
                } else {
                    die('something went wrong');
                }

            } else {
                $this->view('users/register', $data);
            }
        } else {
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_error' => '',
                'email_error' => '',
                'password_error' => '',
                'confirm_password_error' => '',
            ];

            $this->view('users/register', $data);
        }
    }

    /**
     *
     */
    public function login()
    {
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              //sanitize post
              $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

              $data = [
                  'email' => trim($_POST['email']),
                  'password' => trim($_POST['password']),
                  'email_error' => '',
                  'password_error' => '',
              ];

              if (empty($data['email'])) {
                  $data['email_error'] = 'Please enter email';
              }

              if (empty($data['password'])) {
                  $data['password_error'] = 'Please enter password';
              }

              //check for user/email
              if ($this->userModel->findUserByEmail($data['email'])) {
                  //user found
              } else {
                  $data['email_error'] = ' No user found';
              }

              if (empty($data['email_error']) && empty($data['password_error'])) {
                  //check and set logged in user
                  $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                  if ($loggedInUser) {
                      //Create session
                      $this->createUserSession($loggedInUser);
                  } else {
                      $data['password_error'] = 'Password incorrect';
                      $this->view('users/login', $data);
                  }
              } else {
                  $this->view('users/login', $data);
              }
        } else {
            $data = [
                'email' => '',
                'password' => '',
                'email_error' => '',
                'password_error' => '',
            ];

            $this->view('users/login', $data);
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('users/login');
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        redirect('/posts');
    }



}