--------------------------------------------------------
--  REGISTER PROCEDURE
--------------------------------------------------------
create or replace PROCEDURE register
(username IN varchar2, password IN varchar2, name IN varchar2, email IN varchar2, country IN varchar2, city IN varchar2)
IS
 varos_id number := 0;
 user_id number := 0;
BEGIN
  BEGIN
    SELECT ID INTO varos_id FROM VAROSOK WHERE orszag=country and varos=city;
    EXCEPTION
      WHEN NO_DATA_FOUND THEN
        varos_id := 0;
  END;
  IF varos_id = 0 THEN
    varos_id := city_seq.nextval;
    INSERT INTO VAROSOK VALUES(varos_id, city, country);
  END IF;
  user_id := user_seq.nextval;
  INSERT INTO FELHASZNALOK VALUES(user_id,name,email,CURRENT_DATE,CURRENT_DATE,varos_id,null);
  INSERT INTO BEJELENTKEZESI_ADATOK VALUES(username,password,user_id);
END;

--------------------------------------------------------
--  User verifier PROCEDURE
--------------------------------------------------------
create or replace PROCEDURE verifyUser
(username IN VARCHAR2, password IN VARCHAR2, user_id OUT NUMBER)
IS
tmp number:=0;
BEGIN
  BEGIN
    SELECT FELH_ID INTO tmp FROM BEJELENTKEZESI_ADATOK WHERE FELHASZNALONEV = username AND JELSZO = password;
    EXCEPTION WHEN NO_DATA_FOUND THEN
      tmp := 0;
  END;
  IF tmp > 0 THEN
    UPDATE FELHASZNALOK SET UTOLSO_BEJ = CURRENT_DATE WHERE ID = tmp;
  END IF;
  user_id := tmp;
END;

--------------------------------------------------------
--  Album creator PROCEDURE
--------------------------------------------------------
create or replace PROCEDURE create_album
(name IN varchar2, description IN varchar2, user_id IN number, id OUT number, create_time OUT varchar2)
IS
  c_time DATE := SYSDATE;
BEGIN
  id := album_seq.nextval;
  create_time := TO_CHAR(c_time, 'YYYY/MM/DD HH24:MI:SS');
  INSERT INTO ALBUMOK (ID, NEV, LEIRAS, LETREHOZAS_IDEJE, FELH_ID) VALUES (id, name, description, c_time, user_id);
END;

--------------------------------------------------------
--  Album creator PROCEDURE
--------------------------------------------------------
create or replace PROCEDURE upload_picture
(user_id IN number, album_id IN NUMBER, cat_id IN NUMBER, description IN varchar2, place IN varchar2, pic OUT BLOB, id OUT number, create_time OUT STRING)
IS
  c_time DATE := SYSDATE;
BEGIN
  id := image_seq.nextval;
  create_time := TO_CHAR(c_time, 'YYYY/MM/DD HH24:MI:SS');
  INSERT INTO KEPEK (ID, LEIRAS, FELTOLTES_IDEJE, HELYSZIN, KEPFAJL, ALBUM_ID, FELH_ID, KAT_ID) VALUES (id, description, c_time, place, EMPTY_BLOB(), album_id, user_id, cat_id)
  RETURN KEPFAJL INTO pic;
END;