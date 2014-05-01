--------------------------------------------------------
--  DDL for Table ALBUMOK
--------------------------------------------------------

  CREATE TABLE  "ALBUMOK" 
   (	"ID" NUMBER(10,0), 
	"NEV" VARCHAR2(63 BYTE), 
	"LEIRAS" VARCHAR2(512 BYTE), 
	"LETREHOZAS_IDEJE" DATE, 
	"FELH_ID" NUMBER(10,0)
   ) ;
--------------------------------------------------------
--  DDL for Table BEJELENTKEZESI_ADATOK
--------------------------------------------------------

  CREATE TABLE  "BEJELENTKEZESI_ADATOK" 
   (	"FELHASZNALONEV" VARCHAR2(20 BYTE), 
	"JELSZO" VARCHAR2(20 BYTE), 
	"FELH_ID" NUMBER(10,0)
   ) ;

   COMMENT ON COLUMN  "BEJELENTKEZESI_ADATOK"."FELHASZNALONEV" IS 'felhasznalo nev';
--------------------------------------------------------
--  DDL for Table ERTEKELESEK
--------------------------------------------------------

  CREATE TABLE  "ERTEKELESEK" 
   (	"FELH_ID" NUMBER(10,0), 
	"KEP_ID" NUMBER(10,0), 
	"ERTEKELES" NUMBER(1,0)
   ) ;
--------------------------------------------------------
--  DDL for Table FELHASZNALOK
--------------------------------------------------------

  CREATE TABLE  "FELHASZNALOK" 
   (	"ID" NUMBER(10,0), 
	"NEV" VARCHAR2(64 BYTE), 
	"EMAIL" VARCHAR2(128 BYTE), 
	"UTOLSO_BEJ" DATE, 
	"REGISZTR_IDO" DATE, 
	"VAROS_ID" NUMBER(10,0), 
	"AVATAR" BLOB
   ) ;

   COMMENT ON COLUMN  "FELHASZNALOK"."ID" IS 'azonosito';
   COMMENT ON COLUMN  "FELHASZNALOK"."NEV" IS 'nev';
   COMMENT ON COLUMN  "FELHASZNALOK"."UTOLSO_BEJ" IS 'utolso bejelentkezes';
   COMMENT ON COLUMN  "FELHASZNALOK"."REGISZTR_IDO" IS 'regisztracio ideje';
--------------------------------------------------------
--  DDL for Table HOZZASZOLASOK
--------------------------------------------------------

  CREATE TABLE  "HOZZASZOLASOK" 
   (	"ID" NUMBER(10,0), 
	"MEGJEGYZES" VARCHAR2(512 BYTE), 
	"IDOBELYEG" DATE, 
	"FELH_ID" NUMBER(10,0), 
	"KEP_ID" NUMBER(10,0), 
	"VALASZ_ID" NUMBER(10,0)
   ) ;
--------------------------------------------------------
--  DDL for Table KATEGORIAK
--------------------------------------------------------

  CREATE TABLE  "KATEGORIAK" 
   (	"ID" NUMBER(10,0), 
	"KATEGORIA" VARCHAR2(64 BYTE)
   ) ;
--------------------------------------------------------
--  DDL for Table KEPEK
--------------------------------------------------------

  CREATE TABLE  "KEPEK" 
   (	"ID" NUMBER(10,0), 
	"LEIRAS" VARCHAR2(128 BYTE), 
	"FELTOLTES_IDEJE" DATE, 
	"HELYSZIN" VARCHAR2(128 BYTE), 
	"KEPFAJL" BLOB, 
	"ALBUM_ID" NUMBER(10,0), 
	"FELH_ID" NUMBER(10,0), 
	"KAT_ID" NUMBER(10,0)
   ) ;
--------------------------------------------------------
--  DDL for Table VAROSOK
--------------------------------------------------------

  CREATE TABLE  "VAROSOK" 
   (	"ID" NUMBER(10,0), 
	"VAROS" VARCHAR2(64 BYTE), 
	"ORSZAG" VARCHAR2(64 BYTE)
   ) ;
--------------------------------------------------------
--  DDL for Index ALBUM_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "ALBUM_PK" ON  "ALBUMOK" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index BEJELENTKEZES_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "BEJELENTKEZES_PK" ON  "BEJELENTKEZESI_ADATOK" ("FELHASZNALONEV") 
  ;
--------------------------------------------------------
--  DDL for Index ERTEKELESEK_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "ERTEKELESEK_PK" ON  "ERTEKELESEK" ("FELH_ID", "KEP_ID") 
  ;
--------------------------------------------------------
--  DDL for Index FELHASZNALO_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "FELHASZNALO_PK" ON  "FELHASZNALOK" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index HOZZASZOLASOK_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "HOZZASZOLASOK_PK" ON  "HOZZASZOLASOK" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index KATEGORIAK_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "KATEGORIAK_PK" ON  "KATEGORIAK" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index KEPEK_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "KEPEK_PK" ON  "KEPEK" ("ID") 
  ;
--------------------------------------------------------
--  DDL for Index VAROSOK_PK
--------------------------------------------------------

  CREATE UNIQUE INDEX  "VAROSOK_PK" ON  "VAROSOK" ("ID") 
  ;
--------------------------------------------------------
--  Constraints for Table ALBUMOK
--------------------------------------------------------

  ALTER TABLE  "ALBUMOK" ADD CONSTRAINT "ALBUM_PK" PRIMARY KEY ("ID") ENABLE;
  ALTER TABLE  "ALBUMOK" MODIFY ("FELH_ID" NOT NULL ENABLE);
  ALTER TABLE  "ALBUMOK" MODIFY ("NEV" NOT NULL ENABLE);
  ALTER TABLE  "ALBUMOK" MODIFY ("ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table BEJELENTKEZESI_ADATOK
--------------------------------------------------------

  ALTER TABLE  "BEJELENTKEZESI_ADATOK" ADD CONSTRAINT "BEJELENTKEZES_PK" PRIMARY KEY ("FELHASZNALONEV") ENABLE;
  ALTER TABLE  "BEJELENTKEZESI_ADATOK" MODIFY ("FELH_ID" NOT NULL ENABLE);
  ALTER TABLE  "BEJELENTKEZESI_ADATOK" MODIFY ("JELSZO" NOT NULL ENABLE);
  ALTER TABLE  "BEJELENTKEZESI_ADATOK" MODIFY ("FELHASZNALONEV" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table ERTEKELESEK
--------------------------------------------------------

  ALTER TABLE  "ERTEKELESEK" ADD CONSTRAINT "ERTEKELESEK_PK" PRIMARY KEY ("FELH_ID", "KEP_ID") ENABLE;
  ALTER TABLE  "ERTEKELESEK" MODIFY ("ERTEKELES" NOT NULL ENABLE);
  ALTER TABLE  "ERTEKELESEK" MODIFY ("KEP_ID" NOT NULL ENABLE);
  ALTER TABLE  "ERTEKELESEK" MODIFY ("FELH_ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table FELHASZNALOK
--------------------------------------------------------

  ALTER TABLE  "FELHASZNALOK" ADD CONSTRAINT "FELHASZNALO_PK" PRIMARY KEY ("ID") ENABLE;
  ALTER TABLE  "FELHASZNALOK" MODIFY ("REGISZTR_IDO" NOT NULL ENABLE);
  ALTER TABLE  "FELHASZNALOK" MODIFY ("UTOLSO_BEJ" NOT NULL ENABLE);
  ALTER TABLE  "FELHASZNALOK" MODIFY ("NEV" NOT NULL ENABLE);
  ALTER TABLE  "FELHASZNALOK" MODIFY ("ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table HOZZASZOLASOK
--------------------------------------------------------

  ALTER TABLE  "HOZZASZOLASOK" ADD CONSTRAINT "HOZZASZOLASOK_PK" PRIMARY KEY ("ID") ENABLE;
  ALTER TABLE  "HOZZASZOLASOK" MODIFY ("ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table KATEGORIAK
--------------------------------------------------------

  ALTER TABLE  "KATEGORIAK" ADD CONSTRAINT "KATEGORIAK_PK" PRIMARY KEY ("ID") ENABLE;
  ALTER TABLE  "KATEGORIAK" MODIFY ("ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table KEPEK
--------------------------------------------------------

  ALTER TABLE  "KEPEK" ADD CONSTRAINT "KEPEK_PK" PRIMARY KEY ("ID") ENABLE;
  ALTER TABLE  "KEPEK" MODIFY ("ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Constraints for Table VAROSOK
--------------------------------------------------------

  ALTER TABLE  "VAROSOK" ADD CONSTRAINT "VAROSOK_PK" PRIMARY KEY ("ID") ENABLE;
  ALTER TABLE  "VAROSOK" MODIFY ("ORSZAG" NOT NULL ENABLE);
  ALTER TABLE  "VAROSOK" MODIFY ("VAROS" NOT NULL ENABLE);
  ALTER TABLE  "VAROSOK" MODIFY ("ID" NOT NULL ENABLE);
--------------------------------------------------------
--  Ref Constraints for Table ALBUMOK
--------------------------------------------------------

  ALTER TABLE  "ALBUMOK" ADD CONSTRAINT "ALBUMOK_FK_F" FOREIGN KEY ("FELH_ID")
	  REFERENCES  "FELHASZNALOK" ("ID") ON DELETE CASCADE ENABLE;
--------------------------------------------------------
--  Ref Constraints for Table BEJELENTKEZESI_ADATOK
--------------------------------------------------------

  ALTER TABLE  "BEJELENTKEZESI_ADATOK" ADD CONSTRAINT "BEJELENTKEZESI_ADATOK_FK1" FOREIGN KEY ("FELH_ID")
	  REFERENCES  "FELHASZNALOK" ("ID") ON DELETE CASCADE ENABLE;
--------------------------------------------------------
--  Ref Constraints for Table ERTEKELESEK
--------------------------------------------------------

  ALTER TABLE  "ERTEKELESEK" ADD CONSTRAINT "ERTEKELESEK_FK1" FOREIGN KEY ("KEP_ID")
	  REFERENCES  "KEPEK" ("ID")  ON DELETE CASCADEENABLE;
  ALTER TABLE  "ERTEKELESEK" ADD CONSTRAINT "ERTEKELESEK_FK_F" FOREIGN KEY ("FELH_ID")
	  REFERENCES  "FELHASZNALOK" ("ID") ON DELETE CASCADE ENABLE;
--------------------------------------------------------
--  Ref Constraints for Table FELHASZNALOK
--------------------------------------------------------

  ALTER TABLE  "FELHASZNALOK" ADD CONSTRAINT "FELHASZNALOK_FK_V" FOREIGN KEY ("VAROS_ID")
	  REFERENCES  "VAROSOK" ("ID") ENABLE;
--------------------------------------------------------
--  Ref Constraints for Table HOZZASZOLASOK
--------------------------------------------------------

  ALTER TABLE  "HOZZASZOLASOK" ADD CONSTRAINT "HOZZASZOLASOK_FK_F" FOREIGN KEY ("FELH_ID")
	  REFERENCES  "FELHASZNALOK" ("ID") ON DELETE CASCADE ENABLE;
  ALTER TABLE  "HOZZASZOLASOK" ADD CONSTRAINT "HOZZASZOLASOK_FK_K" FOREIGN KEY ("KEP_ID")
	  REFERENCES  "KEPEK" ("ID") ON DELETE CASCADE ENABLE;
  ALTER TABLE  "HOZZASZOLASOK" ADD CONSTRAINT "HOZZASZOLASOK_FK_V" FOREIGN KEY ("VALASZ_ID")
	  REFERENCES  "HOZZASZOLASOK" ("ID") ON DELETE CASCADE ENABLE;
--------------------------------------------------------
--  Ref Constraints for Table KEPEK
--------------------------------------------------------

  ALTER TABLE  "KEPEK" ADD CONSTRAINT "KEPEK_FK_A" FOREIGN KEY ("ALBUM_ID")
	  REFERENCES  "ALBUMOK" ("ID") ON DELETE CASCADE ENABLE;
  ALTER TABLE  "KEPEK" ADD CONSTRAINT "KEPEK_FK_F" FOREIGN KEY ("FELH_ID")
	  REFERENCES  "FELHASZNALOK" ("ID") ON DELETE CASCADE ENABLE;
  ALTER TABLE  "KEPEK" ADD CONSTRAINT "KEPEK_FK_K" FOREIGN KEY ("KAT_ID")
	  REFERENCES  "KATEGORIAK" ("ID") ON DELETE SET NULL ENABLE;

--------------------------------------------------------
--  Auto Increment Sequences
--------------------------------------------------------

  CREATE SEQUENCE album_seq;
  CREATE SEQUENCE login_seq;
  CREATE SEQUENCE user_seq;
  CREATE SEQUENCE comment_seq;
  CREATE SEQUENCE category_seq;
  CREATE SEQUENCE image_seq;
  CREATE SEQUENCE city_seq;
  