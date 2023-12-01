-- Drop Tables (in reverse order of creation due to dependencies)
DROP TABLE Needs;
DROP TABLE Contains;
DROP TABLE HarvestDay;
DROP TABLE PlantDisease4;
DROP TABLE PlantDisease3;
DROP TABLE PlantDisease1;
DROP TABLE WaterLog;
DROP TABLE Crop2;
DROP TABLE Crop1;
DROP TABLE Field;
DROP TABLE Seed;
DROP TABLE Tool;
DROP TABLE StorageUnits;
DROP TABLE Inventory;
DROP TABLE Farmer;

CREATE TABLE Farmer (
    farmerID INTEGER NOT NULL,
    farmerName VARCHAR(25) NOT NULL,
    PRIMARY KEY (farmerID)
);

CREATE TABLE Inventory (
    inventoryID INTEGER NOT NULL,
    inventoryType VARCHAR(25) NOT NULL, -- 'Tool' or 'Seed'
    PRIMARY KEY (inventoryID)
);

CREATE TABLE StorageUnits (
    storageUnitID INTEGER NOT NULL,
    capacity FLOAT NOT NULL CHECK (capacity >= 0),
    farmerID INTEGER NOT NULL,
    PRIMARY KEY (storageUnitID),
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE
);

CREATE TABLE Tool (
    inventoryID INTEGER NOT NULL,
    storageUnitID INTEGER NOT NULL,
    condition INTEGER NOT NULL,
    toolName VARCHAR(25) NOT NULL,
    farmerID INTEGER NOT NULL,
    PRIMARY KEY (inventoryID),
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE,
    FOREIGN KEY (storageUnitID) REFERENCES StorageUnits(storageUnitID)
        ON DELETE CASCADE,
    FOREIGN KEY (inventoryID) REFERENCES Inventory(inventoryID)
        ON DELETE CASCADE
);

CREATE TABLE Seed (
    inventoryID INTEGER NOT NULL,
    storageUnitID INTEGER NOT NULL,
    seedQuantity INTEGER NOT NULL CHECK (seedQuantity >= 0),
    seedType VARCHAR(20) NOT NULL,
    farmerID INTEGER NOT NULL,
    PurchaseDate DATE NOT NULL,
    expiryDate DATE NOT NULL,
    quantityPurchased INTEGER NOT NULL CHECK (quantityPurchased >= 0),
    PRIMARY KEY (inventoryID),
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE,
    FOREIGN KEY (storageUnitID) REFERENCES StorageUnits(storageUnitID)
        ON DELETE CASCADE,
    FOREIGN KEY (inventoryID) REFERENCES Inventory(inventoryID)
        ON DELETE CASCADE
);

CREATE TABLE Field (
    fieldID INTEGER NOT NULL,
    isPlanted NUMBER(1),
    soilType VARCHAR(25) NOT NULL,
    fieldSize FLOAT NOT NULL CHECK (fieldSize >= 0),
    farmerID INTEGER NOT NULL,
    PRIMARY KEY (fieldID),
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE
);

CREATE TABLE Crop1 (
    cropName VARCHAR(25) NOT NULL,
    cropType VARCHAR(25) NOT NULL,
    PRIMARY KEY (cropName)
);

CREATE TABLE Crop2 ( 
    cropID INTEGER NOT NULL,
    cropName VARCHAR(25) NOT NULL,
    growthDuration INTEGER NOT NULL,
    farmerID INTEGER NOT NULL,
    fieldID INTEGER NOT NULL,
    growStartDate DATE NOT NULL,
    PRIMARY KEY (cropID),
    FOREIGN KEY (cropName) REFERENCES Crop1(cropName)
        ON DELETE CASCADE,
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE,
    FOREIGN KEY (fieldID) REFERENCES Field(fieldID)
        ON DELETE CASCADE
);

CREATE TABLE WaterLog(
	waterLogID INTEGER NOT NULL,
    fieldID INTEGER NOT NULL,
    cropID INTEGER NOT NULL,
    waterDate DATE NOT NULL, 
    waterQuantityUsed INTEGER NOT NULL,
    frequencyPerDay INTEGER NOT NULL CHECK (frequencyPerDay >= 0),
    farmerID INTEGER NOT NULL,
    PRIMARY KEY (waterLogID),
    FOREIGN KEY (fieldID) REFERENCES Field(fieldID)
        ON DELETE CASCADE,
    FOREIGN KEY (cropID) REFERENCES Crop2(cropID)
        ON DELETE CASCADE,
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE
);

CREATE TABLE PlantDisease1 (
    symptoms VARCHAR(100) NOT NULL,
    treatment VARCHAR(100),
    PRIMARY KEY (symptoms)
);

CREATE TABLE PlantDisease3 (
    diseaseName VARCHAR(40) NOT NULL,
    symptoms VARCHAR(100) NOT NULL,
    PRIMARY KEY (diseaseName),
    FOREIGN KEY (symptoms) REFERENCES PlantDisease1(symptoms)
        ON DELETE CASCADE
);

CREATE TABLE PlantDisease4 (
    diseaseID INTEGER NOT NULL,
    cropID INTEGER NOT NULL,
    diseaseName VARCHAR(25) NOT NULL,
    diseaseStartDate DATE NOT NULL,
    diseaseEndDate DATE,
    farmerID INTEGER NOT NULL,
    PRIMARY KEY (diseaseID),
    FOREIGN KEY (diseaseName) REFERENCES PlantDisease3(diseaseName)
        ON DELETE CASCADE,
    FOREIGN KEY (cropID) REFERENCES Crop2(cropID)
        ON DELETE CASCADE,
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE
);

CREATE TABLE HarvestDay (
    cropID INTEGER NOT NULL,
    harvestDate DATE NOT NULL, 
    harvestWeight FLOAT NOT NULL CHECK (harvestWeight >= 0),
    farmerID INTEGER NOT NULL,
    PRIMARY KEY (cropID, harvestDate),
    FOREIGN KEY (cropID) REFERENCES Crop2(cropID)
        ON DELETE CASCADE,
    FOREIGN KEY (farmerID) REFERENCES Farmer(farmerID)
        ON DELETE CASCADE
);

CREATE TABLE Contains (
    storageUnitID INTEGER NOT NULL,
    cropID INTEGER NOT NULL,
    PRIMARY KEY (storageUnitID, cropID),
    FOREIGN KEY (storageUnitID) REFERENCES StorageUnits(storageUnitID)
        ON DELETE CASCADE,
    FOREIGN KEY (cropID) REFERENCES Crop2(cropID)
        ON DELETE CASCADE
);

CREATE TABLE Needs (
    cropID INTEGER NOT NULL,
    inventoryID INTEGER NOT NULL,
    PRIMARY KEY (cropID, inventoryID),
    FOREIGN KEY (cropID) REFERENCES Crop2(cropID)
        ON DELETE CASCADE,
    FOREIGN KEY (inventoryID) REFERENCES Inventory(inventoryID)
        ON DELETE CASCADE
);

-- Insert into Farmer
INSERT INTO Farmer (farmerID, farmerName) VALUES (1, 'John Doe');
INSERT INTO Farmer (farmerID, farmerName) VALUES (2, 'Engy S');
INSERT INTO Farmer (farmerID, farmerName) VALUES (3, 'Amir F');
INSERT INTO Farmer (farmerID, farmerName) VALUES (4, 'Zoe Y');

-- Insert Tools into Inventory
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1001, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1002, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1003, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1004, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1005, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1006, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1007, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1008, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1009, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1010, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1011, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1012, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1013, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1014, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1015, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1016, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1017, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1018, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1019, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1020, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1021, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1022, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1023, 'Tool');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (1024, 'Tool');

-- Insert into StorageUnits
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (1, 1000.5, 1);
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (2, 800.0, 1);

INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (3, 500.25, 2);
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (4, 650.75, 2);
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (5, 750.0, 2);

INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (6, 500.25, 3);
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (7, 650.75, 3);
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (8, 750.0, 3);

INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (9, 830.25, 4);
INSERT INTO StorageUnits (storageUnitID, capacity, farmerID) VALUES (10, 230.75, 4);

-- Insert Seeds into Inventory
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2001, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2002, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2003, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2004, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2005, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2006, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2007, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2008, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2009, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2010, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2011, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2012, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2013, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2014, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2015, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2016, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2017, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2018, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2019, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2020, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2021, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2022, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2023, 'Seed');
INSERT INTO Inventory (inventoryID, inventoryType) VALUES (2024, 'Seed');

-- Farmer 1
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1001, 1, 4, 'Shovel', 1);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1002, 2, 3, 'Hoe', 1);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1003, 1, 5, 'Rake', 1);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1004, 2, 4, 'Tiller', 1);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1005, 1, 2, 'Wheelbarrow', 1);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1006, 1, 1, 'Pruner', 1);
-- Farmer 2
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1007, 3, 4, 'Shovel', 2);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1008, 5, 3, 'Hoe', 2);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1009, 4, 5, 'Rake', 2);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1010, 4, 4, 'Tiller', 2);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1011, 4, 2, 'Wheelbarrow', 2);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1012, 3, 1, 'Pruner', 2);
-- Farmer 3
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1013, 8, 4, 'Shovel', 3);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1014, 6, 3, 'Hoe', 3);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1015, 7, 5, 'Rake', 3);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1016, 8, 4, 'Tiller', 3);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1017, 7, 2, 'Wheelbarrow', 3);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1018, 6, 1, 'Pruner', 3);
-- Farmer 4
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1019, 9, 4, 'Shovel', 4);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1020, 10, 3, 'Hoe', 4);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1021, 10, 5, 'Rake', 4);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1022, 9, 4, 'Tiller', 4);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1023, 10, 2, 'Wheelbarrow', 4);
INSERT INTO Tool (inventoryID, storageUnitID, condition, toolName, farmerID) VALUES (1024, 9, 1, 'Pruner', 4);

-- Insert into Seed
-- Farmer 1
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2001, 1, 100, 'Wheat', 1, TO_DATE('2023-01-01', 'YYYY-MM-DD') ,TO_DATE('2024-01-01', 'YYYY-MM-DD'), 500);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2002, 2, 150, 'Corn', 1, TO_DATE('2023-02-10', 'YYYY-MM-DD')  ,TO_DATE('2024-02-10', 'YYYY-MM-DD'), 400);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2003, 1, 75, 'Rice', 1, TO_DATE('2023-03-15', 'YYYY-MM-DD')   ,TO_DATE('2024-03-15', 'YYYY-MM-DD'), 450);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2004, 2, 50, 'Barley', 1, TO_DATE('2023-05-20', 'YYYY-MM-DD') ,TO_DATE('2024-05-20', 'YYYY-MM-DD'), 300);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2005, 1, 80, 'Soybean', 1, TO_DATE('2023-07-25', 'YYYY-MM-DD'),TO_DATE('2024-07-25', 'YYYY-MM-DD'), 350);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2006, 2, 100, 'Tomatoes', 1, TO_DATE('2023-01-01', 'YYYY-MM-DD') ,TO_DATE('2024-01-01', 'YYYY-MM-DD'), 500);
-- Farmer 2
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2007, 3, 150, 'Lettuce', 2, TO_DATE('2023-02-10', 'YYYY-MM-DD')  ,TO_DATE('2024-02-10', 'YYYY-MM-DD'), 400);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2008, 4, 75, 'Carrots', 2, TO_DATE('2023-03-15', 'YYYY-MM-DD')   ,TO_DATE('2024-03-15', 'YYYY-MM-DD'), 450);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2009, 4, 50, 'Apples', 2, TO_DATE('2023-05-20', 'YYYY-MM-DD') ,TO_DATE('2024-05-20', 'YYYY-MM-DD'), 300);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2010, 4, 80, 'Pumpkins', 2, TO_DATE('2023-07-25', 'YYYY-MM-DD'),TO_DATE('2024-07-25', 'YYYY-MM-DD'), 350);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2011, 5, 100, 'Cabbage', 2, TO_DATE('2023-01-01', 'YYYY-MM-DD') ,TO_DATE('2024-01-01', 'YYYY-MM-DD'), 500);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2012, 5, 150, 'Pears', 2, TO_DATE('2023-02-10', 'YYYY-MM-DD')  ,TO_DATE('2024-02-10', 'YYYY-MM-DD'), 400);
-- Farmer 3
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2013, 6, 75, 'Onions', 3, TO_DATE('2023-03-15', 'YYYY-MM-DD')   ,TO_DATE('2024-03-15', 'YYYY-MM-DD'), 450);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2014, 7, 50, 'Garlic', 3, TO_DATE('2023-05-20', 'YYYY-MM-DD') ,TO_DATE('2024-05-20', 'YYYY-MM-DD'), 300);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2015, 8, 80, 'Spinach', 3, TO_DATE('2023-07-25', 'YYYY-MM-DD'),TO_DATE('2024-07-25', 'YYYY-MM-DD'), 350);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2016, 6, 100, 'Peas', 3, TO_DATE('2023-01-01', 'YYYY-MM-DD') ,TO_DATE('2024-01-01', 'YYYY-MM-DD'), 500);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2017, 7, 150, 'Broccoli', 3, TO_DATE('2023-02-10', 'YYYY-MM-DD')  ,TO_DATE('2024-02-10', 'YYYY-MM-DD'), 400);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2018, 8, 75, 'Grapes', 3, TO_DATE('2023-03-15', 'YYYY-MM-DD')   ,TO_DATE('2024-03-15', 'YYYY-MM-DD'), 450);
-- Farmer 4
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2019, 9, 50, 'Cauliflower', 4, TO_DATE('2023-05-20', 'YYYY-MM-DD') ,TO_DATE('2024-05-20', 'YYYY-MM-DD'), 300);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2020, 10, 80, 'Strawberries', 4, TO_DATE('2023-07-25', 'YYYY-MM-DD'),TO_DATE('2024-07-25', 'YYYY-MM-DD'), 350);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2021, 9, 100, 'Potatoes', 4, TO_DATE('2023-01-01', 'YYYY-MM-DD') ,TO_DATE('2024-01-01', 'YYYY-MM-DD'), 500);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2022, 10, 150, 'Sweet Potatoes', 4, TO_DATE('2023-02-10', 'YYYY-MM-DD')  ,TO_DATE('2024-02-10', 'YYYY-MM-DD'), 400);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2023, 9, 75, 'Tomatoes', 4, TO_DATE('2023-03-15', 'YYYY-MM-DD')   ,TO_DATE('2024-03-15', 'YYYY-MM-DD'), 450);
INSERT INTO Seed (inventoryID, storageUnitID, seedQuantity, seedType, farmerID, PurchaseDate, expiryDate, quantityPurchased) VALUES (2024, 10, 50, 'Lettuce', 4, TO_DATE('2023-05-20', 'YYYY-MM-DD') ,TO_DATE('2024-05-20', 'YYYY-MM-DD'), 300);

-- Insert into Field
INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (1, 1, 'Loamy', 5.0, 1);
INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (2, 1, 'Sandy', 4.5, 1);

INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (3, 1, 'Clay', 6.0, 2);

INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (4, 1, 'Peaty', 3.5, 3);
INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (5, 1, 'Chalky', 4.0, 3);

INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (6, 1, 'Loamy', 5.0, 4);
INSERT INTO Field (fieldID, isPlanted, soilType, fieldSize, farmerID) VALUES (7, 1, 'Sandy', 4.5, 4);


-- Insert into Crop1
INSERT INTO Crop1 (cropName, cropType) VALUES ('Wheat', 'Grain');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Corn', 'Cereal');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Rice', 'Grain');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Barley', 'Cereal');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Soybean', 'Legume');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Broccoli', 'Leafy Green');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Grapes', 'Fruit');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Cauliflower', 'Leafy Green');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Strawberries', 'Fruit');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Potatoes', 'Root/Tuber');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Sweet Potatoes', 'Root/Tuber');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Tomatoes', 'Fruit/Vegetable');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Lettuce', 'Leafy Green');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Carrots', 'Root/Tuber');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Apples', 'Fruit');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Pumpkins', 'Fruit/Vegetable');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Cabbage', 'Leafy Green');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Pears', 'Fruit');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Onions', 'Bulb');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Garlic', 'Bulb');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Spinach', 'Leafy Green');
INSERT INTO Crop1 (cropName, cropType) VALUES ('Peas', 'Legume');

-- Crop2 
-- Farmer 1
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3001, 'Wheat', 120, 1, 1, DATE '2023-03-01');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3002, 'Corn', 90, 1, 2, DATE '2023-03-02');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3003, 'Rice', 150, 1, 2, DATE '2023-03-03');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3004, 'Barley', 75, 1, 1, DATE '2023-03-04');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3005, 'Soybean', 100, 1, 2, DATE '2023-03-05');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3006, 'Tomatoes', 80, 1, 1, DATE '2023-03-10');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3007, 'Lettuce', 55, 1, 1, DATE '2023-03-15');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3008, 'Carrots', 100, 1, 2, DATE '2023-03-20');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3009, 'Apples', 180, 1, 1, DATE '2023-03-25');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3010, 'Pumpkins', 120, 1, 2, DATE '2023-03-30');
-- Farmer 2
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3011, 'Cabbage', 90, 2, 3, DATE '2023-04-01');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3012, 'Pears', 150, 2, 3, DATE '2023-04-02');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3013, 'Onions', 75, 2, 3, DATE '2023-04-03');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3014, 'Garlic', 100, 2, 3, DATE '2023-04-04');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3015, 'Spinach', 100, 2, 3, DATE '2023-04-05');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3016, 'Peas', 100, 2, 3, DATE '2023-04-05');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3017, 'Broccoli', 80, 2, 3, DATE '2023-04-10');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3018, 'Grapes', 55, 2, 3, DATE '2023-04-15');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3019, 'Cauliflower', 100, 2, 3, DATE '2023-04-20');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3020, 'Strawberries', 180, 2, 3, DATE '2023-04-25');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3021, 'Potatoes', 120, 2, 3, DATE '2023-04-30');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3022, 'Wheat', 120, 2, 3, DATE '2023-05-01');    
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3023, 'Corn', 90, 2, 3, DATE '2023-05-02');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3024, 'Rice', 150, 2, 3, DATE '2023-05-03');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3025, 'Barley', 75, 2, 3, DATE '2023-05-04');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3026, 'Soybean', 100, 2, 3, DATE '2023-05-05');
-- Farmer 3
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3027, 'Tomatoes', 80, 3, 4, DATE '2023-05-10'); 
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3028, 'Lettuce', 55, 3, 5, DATE '2023-05-15');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3029, 'Carrots', 100, 3, 4, DATE '2023-05-20');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3030, 'Apples', 180, 3, 4, DATE '2023-05-25');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3031, 'Pumpkins', 120, 3, 5, DATE '2023-05-30');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3032, 'Cabbage', 90, 3, 4, DATE '2023-06-01');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3033, 'Pears', 150, 3, 5, DATE '2023-06-02');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3034, 'Onions', 75, 3, 4, DATE '2023-06-03');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3035, 'Garlic', 100, 3, 5, DATE '2023-06-04');
-- Farmer 4
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3036, 'Spinach', 100, 4, 6, DATE '2023-06-05');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3037, 'Broccoli', 80, 4, 7, DATE '2023-06-10');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3038, 'Grapes', 55, 4, 6, DATE '2023-06-15');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3039, 'Cauliflower', 100, 4, 6, DATE '2023-06-20');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3040, 'Strawberries', 180, 4, 7, DATE '2023-06-25');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3041, 'Potatoes', 120, 4, 6, DATE '2023-06-30');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3042, 'Wheat', 120, 4, 7, DATE '2023-07-01');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3043, 'Corn', 90, 4, 7, DATE '2023-07-02');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3044, 'Rice', 150, 4, 6, DATE '2023-07-03');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3045, 'Barley', 75, 4, 6, DATE '2023-07-04');
INSERT INTO Crop2 (cropID, cropName, growthDuration, farmerID, fieldID, growStartDate) VALUES (3046, 'Soybean', 100, 4, 6, DATE '2023-07-05');

INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (1, 1, 3001, DATE '2023-04-01', 50, 2, 1);
INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (2, 2, 3002, DATE '2023-04-02', 45, 2, 1);
INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (3, 3, 3003, DATE '2023-04-03', 55, 1, 2);
INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (4, 4, 3004, DATE '2023-04-04', 40, 3, 3);
INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (5, 5, 3005, DATE '2023-04-05', 60, 1, 3);
INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (6, 6, 3006, DATE '2023-04-06', 50, 2, 4);
INSERT INTO WaterLog (waterLogID, fieldID, cropID, waterDate, waterQuantityUsed, frequencyPerDay, farmerID) VALUES (7, 7, 3007, DATE '2023-04-07', 45, 2, 4);

INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Yellowing leaves', 'Nutrient supplements');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Stunted growth', 'Pesticides');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Black mold on leaves', 'Antifungal sprays and reduced humidity');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Wilting stems', 'Proper watering and drainage');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Curled leaves', 'Pest control and reduction in environmental stress');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('White powdery spots on leaves and stems', 'Increase air circulation and reduce humidity');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Brown or black spots on leaves', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Swelling or knots in roots', 'Practice crop rotation with non-host crops');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Orange, yellow or red powdery spots on leaves', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Rough, corky spots on fruits and leaves', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Leaves drooping and turning brown', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Mottled and distorted leaves', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Dark, sunken lesions on leaves, stems, or fruits', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('White, powdery coating on leaves and stems', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Yellow spots on leaves with white or grayish patches underneath', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Wilting and yellowing leaves with sunken areas on stems', 'Remove and destroy infected leaves');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Swollen and distorted roots', 'Practice crop rotation with non-host crops');
INSERT INTO PlantDisease1 (symptoms, treatment) VALUES ('Burn-like symptoms on tree branches and twigs', 'Remove and destroy infected leaves');

INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Blight', 'Yellowing leaves');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Fungus', 'Stunted growth');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Mildew', 'White powdery spots on leaves and stems');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Leaf Spot', 'Brown or black spots on leaves');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Root Knot', 'Swelling or knots in roots');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Rust', 'Orange, yellow or red powdery spots on leaves');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Scab', 'Rough, corky spots on fruits and leaves');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Wilt', 'Leaves drooping and turning brown');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Mosaic Virus', 'Mottled and distorted leaves');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Anthracnose', 'Dark, sunken lesions on leaves, stems, or fruits');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Powdery Mildew', 'White, powdery coating on leaves and stems');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Downy Mildew', 'Yellow spots on leaves with white or grayish patches underneath');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Bacterial Canker', 'Wilting and yellowing leaves with sunken areas on stems');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Clubroot', 'Swollen and distorted roots');
INSERT INTO PlantDisease3 (diseaseName, symptoms) VALUES ('Fire Blight', 'Burn-like symptoms on tree branches and twigs');

INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (1, 3001, 'Blight', DATE '2023-05-01', DATE '2023-05-10', 1);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (2, 3002, 'Fungus', DATE '2023-05-05', DATE '2023-05-15', 1);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (3, 3003, 'Mildew', DATE '2023-06-01', DATE '2023-06-11', 1);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (4, 3004, 'Leaf Spot', DATE '2023-06-07', DATE '2023-06-17', 1);

INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (5, 3011, 'Root Knot', DATE '2023-06-15', DATE '2023-06-25', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (6, 3012, 'Rust', DATE '2023-06-01', DATE '2023-06-11', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (7, 3013, 'Scab', DATE '2023-06-07', DATE '2023-06-17', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (8, 3014, 'Wilt', DATE '2023-06-15', DATE '2023-06-25', 2);

INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (9, 3027, 'Mosaic Virus', DATE '2023-06-01', DATE '2023-06-11', 3);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (10, 3028, 'Anthracnose', DATE '2023-06-07', DATE '2023-06-17', 3);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (11, 3029, 'Powdery Mildew', DATE '2023-06-15', DATE '2023-06-25', 3);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (12, 3030, 'Downy Mildew', DATE '2023-06-01', DATE '2023-06-11', 3);

INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (13, 3036, 'Bacterial Canker', DATE '2023-06-07', DATE '2023-06-17', 4);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (14, 3037, 'Clubroot', DATE '2023-06-15', DATE '2023-06-25', 4);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (15, 3038, 'Fire Blight', DATE '2023-06-01', DATE '2023-06-11', 4);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (16, 3039, 'Blight', DATE '2023-06-07', DATE '2023-06-17', 4);

INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (17, 3015, 'Blight', DATE '2023-06-15', DATE '2023-06-25', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (18, 3016, 'Fungus', DATE '2023-06-01', DATE '2023-06-11', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (19, 3017, 'Mildew', DATE '2023-06-07', DATE '2023-06-17', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (20, 3018, 'Mosaic Virus', DATE '2023-06-15', DATE '2023-06-25', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (21, 3019, 'Anthracnose', DATE '2023-06-01', DATE '2023-06-11', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (22, 3020, 'Powdery Mildew', DATE '2023-06-07', DATE '2023-06-17', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (23, 3021, 'Downy Mildew', DATE '2023-06-15', DATE '2023-06-25', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (24, 3022, 'Bacterial Canker', DATE '2023-06-01', DATE '2023-06-11', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (25, 3023, 'Clubroot', DATE '2023-06-07', DATE '2023-06-17', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (26, 3024, 'Fire Blight', DATE '2023-06-15', DATE '2023-06-25', 2);
INSERT INTO PlantDisease4 (diseaseID, cropID, diseaseName, diseaseStartDate, diseaseEndDate, farmerID) VALUES (27, 3025, 'Leaf Spot', DATE '2023-06-19', DATE '2023-06-28', 2);

-- Insert into HarvestDay
INSERT INTO HarvestDay (cropID, harvestDate, harvestWeight, farmerID) VALUES (3001, DATE '2023-09-01', 500.0, 1);
INSERT INTO HarvestDay (cropID, harvestDate, harvestWeight, farmerID) VALUES (3011, DATE '2023-08-20', 450.0, 2);
INSERT INTO HarvestDay (cropID, harvestDate, harvestWeight, farmerID) VALUES (3027, DATE '2023-10-01', 650.0, 3);
INSERT INTO HarvestDay (cropID, harvestDate, harvestWeight, farmerID) VALUES (3036, DATE '2023-07-30', 400.0, 4);

-- Insert into Contains
-- Farmer 1
INSERT INTO Contains (storageUnitID, cropID) VALUES (1, 3001);
-- Farmer 2
INSERT INTO Contains (storageUnitID, cropID) VALUES (3, 3011);
-- Farmer 3
INSERT INTO Contains (storageUnitID, cropID) VALUES (6, 3027);
-- Farmer 4
INSERT INTO Contains (storageUnitID, cropID) VALUES (9, 3036);

-- Insert into Needs
INSERT INTO Needs (cropID, inventoryID) VALUES (3001, 1001);
INSERT INTO Needs (cropID, inventoryID) VALUES (3002, 1002);
INSERT INTO Needs (cropID, inventoryID) VALUES (3003, 2003);
INSERT INTO Needs (cropID, inventoryID) VALUES (3004, 2004);
INSERT INTO Needs (cropID, inventoryID) VALUES (3005, 2005);