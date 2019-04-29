CREATE TABLE EducationalSoftware(SoftwareID INT NOT NULL auto_increment,
UserID INT NOT NULL,
NameOfSoftware VARCHAR(255) NOT NULL,
SoftwareVendor VARCHAR(255) nNOT NULL,
NotesOnSoftware TEXT,
CONSTRAINT EducationalSoftware_PK PRIMARY KEY (SoftwareID),
CONSTRAINT SoftwareUser_FK FOREIGN KEY (UserID) REFERENCES Users(UserID));
