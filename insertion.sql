INSERT INTO QUESTIONNAIRE (questionnaireID, questionnaireName, questionnaireDescription) VALUES 
(1,"Test 1","ORLEANS"),
(2,"Test 2","ORLEANS"),
(3,"Test 3","ORLEANS"),
(4,"Test 4","ORLEANS"),
(5,"Test 5","ORLEANS"),
(6,"Test 6","ORLEANS"),
(7,"Test 7","ORLEANS");

INSERT INTO QUESTION (questionID, questionText, questionType, questionnaireID, questionOrder) VALUES 
(1,"Qui sont les fréres de Luffy ?","checkbox", 1, 0),
(2,"Lequel est mort à Marine Ford ?","radio",1 ,1),
(3,"Qui est le personnage principal ?(seulemnt le prénom)","text",1,2),
(4,"Quel Shichibukai est le rival de Shanks ?","dropdown",1,3);

INSERT INTO REPONSE (reponseID, reponseText, questionID, correct) VALUES
(1,"Ace", 1, true),
(2,"Garp", 1, false),
(3,"Shanks", 1, false),
(4,"Sabo", 1, true),
(5,"Ace", 2, true),
(6,"Garp", 2, false),
(7,"Shanks", 2, false),
(8,"Sabo", 2, false),
(9,"Baggy Le Clown", 4, false),
(10,"Crocodile", 4, false),
(11,"Dracule Mihawk", 4, true),
(12,"Marshal D Teach", 4, false),
(13,"Boa Hancock", 4, false),
(14,"Luffy", 3, true);
