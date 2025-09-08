# Grammar_correction_tool
Grammar_correction_tool using natural language processing to automatically correct the incorrect sentence and also translate the corrected sentence into different indian languages and also additional features such as pos_tags and speech recognition and speech out as well as save the history along with login authentication .

# process of setup the project
# install xampp and start the apache and mysql and then click the both admins 

At first install the xampp and then create a folder in the local disk --> xampp folder --> htdocs --> create a folder --> paste the all files
in that created folder place all the php files along with include folder 
and in phpmyadmin create a database name nlp_project or if you create your own then change the database name in db_connect.php file present in the include folder and then import the sql file in the database it will automatically create the tables.

# now install the python idle and then install the following requirements : 
# Required Packages
# Flask – for the web server

pip install Flask

# flask-cors – to handle CORS

pip install flask-cors

# spacy – for NLP (POS tagging, parsing, etc.)

pip install spacy

# and download the English model:

python -m spacy download en_core_web_sm

# openai – for grammar correction (if you want GPT-based correction)

pip install openai

# gTTS – for text-to-speech

pip install gTTS

# deep-translator – for translation support

pip install deep-translator

# requests (comes with most installs, but ensure it’s there because deep-translator depends on it)

pip install requests


# install all these packages and then open the command prompt to change the directory in which app.py present 

run the command :
# python app.py

It runs a flask server at localhost now open the apache localhost admin through xampp

now navigate the htdocs folder in the xampp localhost server in the default browser then run the folder :

which fist index.php to login authentication and for first users registeration proceess with the respective details and then after complete the registration then it automatically redirecting
to the login page and the login with username or email and password to enter the users dashboard in that dashboard enter the input sentence and then click submit to processing the input to get the output and the with the help of translate button to translate the sentence in different indian main languages as well as speech out button it speech out the output text and also speech recognition 
feature to input the sentence through speech as well as display the parts of speech tags with the respective parts of speeches.

# for every output processing the history will automatically saved to database for future use.

# offline grammar correction does not give the exact response it makes mistakes for such that grammar mistakes as well as incorrect way understand so it is prefer to use the open ai apikey so place the open ai api key you can create your own api key from these steps :

https://platform.openai.com/api-keys
Click “Create new secret key”.
Give it a name (for example: FlaskGrammarApp).
Copy the key shown (it starts with sk-... or sk-proj-...).
⚠️ You’ll only see it once — so save it securely.
Replace your hardcoded OPENAI_KEY in the script with this key.
