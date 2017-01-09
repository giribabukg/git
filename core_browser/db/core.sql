

CREATE TABLE "barcodes" (
"id" INTEGER PRIMARY KEY AUTOINCREMENT,
"orderid" INTEGER,
"barcode1" TEXT,
"barcode2" TEXT,
"barcode3" TEXT,
"barcode4" TEXT,
"barcode5" TEXT,
"barcode6" TEXT
);


CREATE UNIQUE INDEX "ix_barcodes" ON "barcodes" ("orderid");

CREATE TABLE "core" (
"id" INTEGER PRIMARY KEY AUTOINCREMENT,
"orderid" INTEGER,
"salesorderid" INTEGER,
"customerid" TEXT,
"customer" TEXT,
"brand" TEXT,
"subbrand" TEXT,
"category" TEXT,
"packsize" TEXT,
"packtype" TEXT,
"printer" TEXT,
"brandowner" TEXT,
"agency" TEXT,
"supplier" TEXT,
"description" TEXT
);


CREATE UNIQUE INDEX "ix_core_orderid" ON "core" ("orderid");

CREATE TABLE "customerfields" (
"id" INTEGER PRIMARY KEY AUTOINCREMENT,
"orderid" INTEGER,
"field1_label" TEXT,
"field1_value" TEXT,
"field2_label" TEXT,
"field2_value" TEXT,
"field3_label" TEXT,
"field3_value" TEXT,
"field4_label" TEXT,
"field4_value" TEXT,
"field5_label" TEXT,
"field5_value" TEXT
);


CREATE UNIQUE INDEX "ix_customerfields_orderid" ON "customerfields" ("orderid");