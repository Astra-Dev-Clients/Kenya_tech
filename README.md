# README - Setup Guide for the Project

## Import Using Composer

This guide explains how to set up and run the project using Composer, a dependency manager for PHP. Follow the steps below to install the required dependencies and configure your API keys.

---

## 1. Get the Required API Keys
Before running the program, you need to obtain API keys from the following services:

### a) **ElevenLabs**  
- Get your API key from [ElevenLabs](https://elevenlabs.io/).

### b) **Google Gemini AI**  
- Obtain your API key from [Google AI Studio](https://aistudio.google.com/prompts/new_chat).

### c) **Google Authentication**  
- You need to generate the following credentials:
  - `SecretKey`
  - `ClientID`
  - `ClientURI`
- Obtain them from the [Google Cloud Console](https://console.cloud.google.com/).

---

## 2. Install Dependencies Using Composer
Make sure you have [PHP Composer](https://getcomposer.org/download/) installed before proceeding. Then, run the following commands in your terminal:

### a) Install Google API Client
```sh
composer require google/apiclient
```

### b) Install Dotenv (for Environment Variable Management)
```sh
composer search phpdotenv  # Optional: Search for dotenv packages
composer require vlucas/phpdotenv
```

### c) Install Guzzle (for HTTP Requests)
```sh
composer search guzzle  # Optional: Search for Guzzle packages
composer require guzzlehttp/guzzle
```

### d) Install Markdown Parsing Library
```sh
composer require erusev/parsedown
```

### e) Install Symfony Mailer & PHPMailer
```sh
composer require symfony/mailer
composer require phpmailer/phpmailer
```

---

## 3. Set Up Environment Variables
Create a `.env` file in the root directory and add the following API keys and credentials:

```ini
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

GOOGLE_CLASSROOM_CLIENT_ID=
GOOGLE_CLASSROOM_CLIENT_SECRET=
GOOGLE_CLASSROOM_REDIRECT_URI=

GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=

MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_REDIRECT_URI=

ELEVENLABS_APIKEY=
ELEVENLABS_VOICE_MARK=
ELEVENLABS_VOICE_ADAM=
ELEVENLABS_VOICE_KIPKOECH=
ELEVENLABS_VOICE_ALEX=
ELEVENLABS_VOICE_JOE=
ELEVENLABS_VOICE_ANETTE=
ELEVENLABS_VOICE_ANNAH=

GOOGLE_GEMINI_URL=
GOOGLE_GEMINI_API=

ASSEMBLYAI_APIKEY=
REVAI_Access_Token=

GMAIL_APP_PASSWORD=
GMAIL_USERNAME=
```

Ensure that the `.env` file is not shared publicly or committed to version control to keep your API keys secure.

---

## 4. Running the Project
Once all dependencies are installed and environment variables are set up, you can start using the application as intended. If you encounter any issues, check that:
- All required API keys are correctly configured.
- Composer dependencies are installed properly.
- Your PHP version meets the project's requirements.

---

## 5. Troubleshooting
If you run into any issues, try the following:
- Run `composer update` to ensure all dependencies are up to date.
- Double-check that your API keys are correctly set in the `.env` file.
- Ensure your PHP installation has the necessary extensions enabled.

For further assistance, reach out to the team or check the official documentation for each library.

---

## 6. Run Server

 php -S localhost:443 -t . -c php.ini


## 6. Additional Notes
- Make sure you restart your server after making changes to the `.env` file.
- For security reasons, never expose your API keys in public repositories.

---

Happy Coding! 🚀

