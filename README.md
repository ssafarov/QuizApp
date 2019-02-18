# Quizz application
PHP MVC with PDO and Bootstrap


## Required
* PHP 5.6+,
* MySQL server
* WAMPP, XAMPP, etc

## Configuration
1. Create the database and import SQL file from the .sql-dumps folder
2. Set the DB access params in the app/Config.php file
3. Set the appropriate app name and base URL.
4. Run the composer from the app root folder with the following argumetns:
  <pre>composer install</pre> and <pre>composer dump-autoload</pre>
5. Set the doc root folder of the web server to app/public folder

## Results
Quiz App will run on BASE_URL address.

## Setting new quiz
System allowing to manage: create/delete quizzes.

### Creating new quiz
1. Goto to Control center and hit the "Add new quiz" button.
2. Give the quiz title and short description
3. Add new question
4. Add seeral answers and set the right one.
5. Repeat with question creation with amount you need.

### Taking the quiz
1. Click the "take a quiz" link
2. Type your name and select the quiz from the dropdown list. (There will be only quizzes with registered questions)
3. Select the answer and click "Next"
4. After last question you'll see your score.

Enkoy with play
