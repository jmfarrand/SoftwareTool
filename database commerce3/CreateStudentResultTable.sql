CREATE TABLE studentresults(resultid SERIAL PRIMARY KEY,
softwareid INTEGER NOT NULL REFERENCES educationalsoftware(softwareID),
userid INTEGER NOT NULL REFERENCES users(userid),
studentfname VARCHAR(255) NOT NULL,
studentlname VARCHAR(255) NOT NULL,
testtype VARCHAR(255) NOT NULL,
testscore VARCHAR(255) NOT NULL,
studentcomments TEXT);
