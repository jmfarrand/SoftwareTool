CREATE TABLE educationalsoftware(softwareid SERIAL PRIMARY KEY,
userid INTEGER NOT NULL REFERENCES users(userid),
nameofsoftware VARCHAR(255) NOT NULL,
softwarevendor VARCHAR(255) NOT NULL,
notesonsoftware TEXT);
