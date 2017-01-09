<?php
//General
define('SITE_NAME', 'Auftragszettel');
define('ERROR_OCCURRED', 'Ein Fehler ist aufgetreten');
define('SERVICE_ORDER_SEARCH', 'Auftragssuche');
define('SERVICE_ORDER_RECENT', 'Auftragshistorie');

define('ORDER_DETAILS', 'Auftragsdetails');
define('SALES_ORDER', 'Kundenauftrag');
define('SKU_MATERIAL_NUMBER', 'SKU Materialnummer');
define('CUSTOMER_MATERIAL_NUMBER', 'Kunden Materialnummer');
define('CUSTOMER_DESCRIPTION', 'Kundenbezeichnung');
define('PRINT_METHOD', 'Druckart');
define('PRINT_MACHINE', 'Druckmaschine');
define('DUE_DATE', 'End Datum');

define('CONTACTS', 'Kontakte');
define('CONTACTS_AP', 'Ansprechpartner');
define('CONTACTS_ZM', 'Sachbearbeiter');
define('CONTACTS_VE', 'Außendienst');
define('CONTACTS_WE', 'Warenempfänger');
define('CONTACTS_AG', 'Auftraggeber');

define('PARTNERS', 'Partner');
define('PARTNERS_ZN', 'Drucker');
define('PARTNERS_ZO', 'Markenartikler');
define('PARTNERS_ZR', 'Agentur');
define('PARTNERS_ZS', 'Abpacker');

define('OPERATIONS', 'Vorgang');
define('CONFIRMED_OPERATIONS', 'Abgeschlossene Arbeitsschritte');
define('COLOR_ROTATION', 'Farbaufbau');
define('DATE_FORMAT', 'd.m.Y');

//GenSpec Corrugated
define('PRINTING_MATERIAL', 'Bedruckstoff');
define('ZLP_AG_C_CORRUGATED_TYPE', 'Wellenart');
define('ZLP_AG_C_SURFACE', 'Materialdecke');
define('ZLP_AG_C_SHEET_FORMAT', 'Bogenformat');
define('ZLP_AG_C_PRINTSIZE_MAX_WIDTH', 'Max. Druckgröße Breite');
define('ZLP_AG_C_PRINTSIZE_MAX_HEIGHT', 'Max. Druckgröße Höhe');
define('ZLP_AG_C_PRINTSIZE_MIN_WIDTH', 'Min. Druckgröße Breite');
define('ZLP_AG_C_PRINTSIZE_MIN_HEIGHT', 'Min. Druckgröße Höhe');

define('COLOURS', 'Farben');
define('ZLP_ANZF_VS', 'Anzahl Farben Job');
define('ZLP_AG_F_MACHINE_COLORS', 'Max. Farbanzahl Maschine');
define('ZLP_REP_F_MAX_INK_COVERAGE', 'max. Farbdeckung');

define('ZLP_AG_F_COLOR', 'Farbbezeichnung');
define('ZLP_AG_F_PLATE_TYPE', 'Plattentyp');
define('ZLP_AG_F_PLATE_TYPE_HELP', 'Plattentyp Hilfstabelle');
define('ZLP_AG_F_PLATE_THICKNESS', 'Plattenstärke');
define('ZLP_AG_F_SPECIAL_RELIEF', 'spez. Relief');
define('ZLP_AG_F_CLICHE_NUMBER', 'Klischeenummer');
define('ZLP_AG_F_LINE_SCREEN', 'Strich/Raster');
define('ZLP_AG_F_ANGLE', 'Winkel');
define('ZLP_AG_F_CONT_SCR_CT', 'Halbton Rasterweite');
define('ZLP_AG_F_TECH_SCR_CT', 'techn. Rasterweite');
define('ZLP_AG_F_DOT_TYPE', 'Punktform');
define('ZLP_AG_F_DISTORTION_L', 'Verzerrungfaktor LR');
define('ZLP_AG_F_DISTORTION_C', 'Verzerrungfaktor QR');
define('ZLP_AG_F_REMARK', 'Bemerkungen');
define('ZLP_AG_F_PATCH', 'Patch');

//CAD Corrugated
define('ZLP_CAD_F_DATA_SOURCE', 'Datenquelle');
define('ZLP_CAD_C_DIE_CUT_NO', 'CAD Nr.');
define('ZLP_CAD_C_CUTTING_NO', 'Schnitt Nr.');
define('ZLP_CAD_C_TOOL_NO', 'Werkzeug Nr.');
define('ZLP_CAD_C_PREV_DIE_CUT_NO', 'Alte CAD Nr.');
define('ZLP_CAD_C_PREV_JOB_NO', 'Alte Auftragsnr.');
define('ZLP_CAD_F_CAD_VIEW', 'CAD Ansicht');
define('ZLP_CAD_F_IN_LINE_TO', 'ausgerichtet nach');
define('ZLP_CAD_F_PASS_FILE', 'Pass-Datei/CAD Verzeichnis');
define('ZLP_CAD_C_3D_CAD', '3D CAD');
define('ZLP_CAD_C_CONSTRUCTION', 'Anlage');
define('ZLP_CAD_C_ADHERING', 'Klebung');
define('ZLP_CAD_F_PRINT_VIEW', 'Druckansicht');
define('ZLP_CAD_F_DIE_CUT_NAME', 'Stanzname');
define('ZLP_CAD_F_NOTES', 'Hinweise');

define('PROCESSING_SACK', 'Processing Sack');
define('ZLP_CAD_C_RS1', 'RS 1');
define('ZLP_CAD_C_SF_01', 'SF');
define('ZLP_CAD_C_VS', 'VS');
define('ZLP_CAD_C_SF_02', 'SF');
define('ZLP_CAD_C_RS2', 'RS 2');
define('ZLP_CAD_C_ADH_BACK', 'Klebung Rückseite');
define('ZLP_CAD_C_ADH_SIDE', 'Klebeseite');
define('ZLP_CAD_C_BOTTOM_HGT', 'Bodenhöhe');

define('PROCESSING_WFK', 'Processing WFK');
define('ZLP_CAD_C_GLUE_LAP', 'Klebelasche');
define('ZLP_CAD_C_SIDE_01', '1. Seite');
define('ZLP_CAD_C_SIDE_02', '2. Seite');
define('ZLP_CAD_C_SIDE_03', '3. Seite');
define('ZLP_CAD_C_SIDE_04', '4. Seite');
define('ZLP_CAD_C_HEIGHT', '5. Höhe');
define('ZLP_CAD_C_LID', '6. Deckel');
define('ZLP_CAD_C_BOTTOM', '7. Boden');

//Repro Corrugated
define('TRAPPING_BLEED', 'Trapping/Überdrücke');
define('ZLP_REP_F_MIN_TRAP', 'Minimale Überfüllung');
define('ZLP_REP_F_MAX_TRAP', 'Maximale Überfullung');
define('ZLP_REP_F_STD_TRAP', 'Standard Überfüllung');
define('ZLP_REP_F_VARNISH_TRAP', 'Lack Überfüllung');

define('ARTWORK_INFORMATION', 'Motivinformation');
define('ZLP_REP_F_MIN_LINE_POS', 'min. Linienstärke pos.');
define('ZLP_REP_F_MIN_LINE_NEG', 'min. Linienstärke neg.');
define('ZLP_REP_F_MIN_TXT_SIZE_POS', 'min. Textgröße pos.');
define('ZLP_REP_F_MIN_TXT_SIZE_NEG', 'min. Textgröße neg.');
define('ZLP_BB_MIN_DOT', 'kleinste Punktgröße Datei');
define('ZLP_REP_F_MAX_TON_VAL', 'maximaler Tonwert im Verlauf');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'Max. Farbdeckung');
define('ZLP_REP_C_RESY', 'Resy Zeichen');
define('ZLP_REP_C_FSC_LOGO', 'FSC-Logo');
define('ZLP_REP_C_FSC_LOGO_VAR', 'FSC-Logo variante');

define('BLEED', 'Überdrücke');
define('ZLP_REP_C_GENERAL_BLEED', 'Überdruck über Schnitt');
define('ZLP_REP_C_MIN_DIST_DIE_CUT', 'Mindestabstand zu Schnittkante');
define('ZLP_REP_C_BLEED_TRAP_LAP', 'Überdruck in Lasche');
define('ZLP_REP_C_BLEED_TRAP_TOP_BOT', 'Überdruck in Boden/Deckel');
define('ZLP_REP_C_MIN_DIST_CREASING', 'Min. Abstand Druck zum Riller');
define('ZLP_REP_C_NOTES', 'Hinweise/Sonstiges');

//StepRepeat Corrugated
//define('ZLP_CAD_C_DIE_CUT_NO', 'CAD Nummer');
define('ZLP_GES_C_MULTIUP', 'Gesamtform vom Kunden');
define('ZLP_GES_C_1UP_RADIAL', 'Nutzenanzahl Radial');
define('ZLP_GES_C_RAP_RADIAL', 'Rapport Radial');
define('ZLP_GES_C_STAG_RADIAL', 'Versatz Radial');
define('ZLP_GES_C_1UP_AXIAL', 'Nutzenanzahl Axial');
define('ZLP_GES_C_RAP_AXIAL', 'Rapport Axial');
define('ZLP_GES_C_STAG_AXIAL', 'Versatz Axial');
define('ZLP_GES_C_1UP_TOTAL', 'Gesamtnutzenanzahl');
define('ZLP_GES_F_REG_MARK_TYPE', 'Passer Typ/Größe');
define('ZLP_GES_F_REG_MARK_POS', 'Passer Position');
define('ZLP_GES_F_ALIGNMENT_BAR', 'Führungsstreifen');
define('ZLP_GES_F_ALIGNMENT_BAR_POS', 'Führungsstreifen Position');
define('ZLP_GES_F_PRINT_CTRL_ELEMENTS', 'Druckkontrollelemente');
define('ZLP_GES_C_1UP_MARK', 'Nutzen Kennzeichnung');
define('ZLP_GES_C_NOTES', 'Sonstiges');

//Barcode
define('ZLP_BC_BWR', 'Repro_BWR');
define('ZLP_BC_CODE_TYPE', 'Repro_Codeart');
define('ZLP_BC_CODE_COLOR', 'Repro_Codefarbe');
define('ZLP_BC_CODE_NUMBER', 'Repro_Codenummer');
define('ZLP_BC_BCKGRND_COLOR', 'Repro_Fondfarbe');
define('ZLP_BC_TEST_PROTOCOL', 'Repro_Pruefprotokoll');
define('ZLP_BC_MAGNIFICATION', 'Magnification');
define('ZLP_BC_RESOLUTION', 'Auflösung');
define('ZLP_BC_NARROW_BAR', 'Narrow bar');
define('ZLP_BC_RATIO', 'Ratio');
define('ZLP_BC_DEVICE_COMPENSATION', 'Device compensation');
define('ZLP_BC_CODE_SIZE', 'SC_Codegroesse');
define('ZLP_BC_SUB_TYPE', 'Datamatrix Subtype');

//CSR
define('CUSTOMER_FILES', 'Kundendaten');
define('ZLP_AV_REF_JOB_NR', 'Referenzjobnr.');
define('ZLP_AV_AMOUNT', 'Anzahl');
define('ZLP_AV_DATA_SOURCE', 'Datenquelle');
define('ZLP_AV_CONVERT_TO', 'Konvertieren für');
define('ZLP_AV_CUST_HARDCOPY_NO', 'Kundendatenausdruck Anzahl');
define('ZLP_AV_CUST_HARDCOPY_SIZE', 'Kundendatenausdruck Format');
define('ZLP_AV_KEEP_LIVE_TEXT', 'Editierbare Schriften für evt. Spätere Textkorrekturen beibehalten');
define('ZLP_AV_PDF_UPLOAD', 'Referenz PDF hochladen');
define('ZLP_AV_NOTES_01', 'Sonstiges');
define('ZLP_AV_RULES_GUIDELINES', 'Regulatorien und Verordnungen');
define('ZLP_AV_DATA_ENTRY_CONFORM', 'Dateneingang konform');
define('ZLP_AV_DATA_HANDLED_CONFORM', 'Datenbearbeitung konform ausführen');

define('TEXT_EDITING', 'Satzarbeiten');
define('ZLP_AV_TEMPLATE', 'Vorlage');

define('REFERENCE_FOR', 'Referenz für');
define('ZLP_AV_TEXT', 'Text');
define('ZLP_AV_POSITION', 'Stand');
define('ZLP_AV_FONT_SIZE', 'Schriftart/-größe');
define('ZLP_AV_NOTES_02', 'Sonstiges');

//CAD Flexibles
//define('ZLP_CAD_F_DATA_SOURCE', 'Datenquelle');
define('ZLP_CAD_F_DIE_CUT_NO', 'CAD Nr.');
define('ZLP_CAD_F_CUTTING_NO', 'Schnitt Nr.');
define('ZLP_CAD_F_TOOL_NO', 'Werkzeug Nr.');
define('ZLP_CAD_F_PREV_DIE_CUT_NO', 'Alte CAD Nr.');
define('ZLP_CAD_F_PREV_JOB_NO', 'Alte Auftragsnr.');
//define('ZLP_CAD_F_CAD_VIEW', 'CAD Ansicht');
//define('ZLP_CAD_F_IN_LINE_TO', 'ausgerichtet nach');
//define('ZLP_CAD_F_PASS_FILE', 'Pass-Datei/CAD Verzeichnis');
define('ZLP_CAD_F_3D_CAD', '3D CAD');
define('ZLP_CAD_F_CONSTRUCTION', 'Anlage');
define('ZLP_CAD_F_ADHERING', 'Klebung');
//define('ZLP_CAD_F_PRINT_VIEW', 'Druckansicht');

define('PROCESSING_FLEXIBLE_BAGS', 'Processing Flexible Bag');
define('PROCESSING_FILM_BAGS', 'Abwicklung Folienbeutel');
define('ZLP_CAD_F_BAG_LENGTH', 'Folienbeutel Länge');
define('ZLP_CAD_F_BAG_WIDTH', 'Folienbeutel Breite');
define('ZLP_CAD_F_BOTTOM_CREASE', 'Bodenfalte');
define('ZLP_CAD_F_FLAP', 'Klappe');

define('PROCESSING_FLAT_FOIL', 'Processing Flat Foil');
define('PROCESSING_FLAT_FILM', 'Abwicklung Flachfolie');
define('ZLP_CAD_F_LENGTH', 'Länge');
define('ZLP_CAD_F_WIDTH', 'Breite');
define('ZLP_CAD_F_PRINT_HEIGHT', 'Druckbildhöhe');
define('ZLP_CAD_F_PRINT_WIDTH', 'Druckbildbreite');
define('ZLP_CAD_F_DISTANCE_LEFT', 'Abstand links');
define('ZLP_CAD_F_DISTANCE_RIGHT', 'Abstand rechts');
define('ZLP_CAD_F_DISTANCE_TOP', 'Abstand oben');
define('ZLP_CAD_F_DISTANCE_BOTTOM', 'Abstand unten');
define('ZLP_CAD_F_MIN_DIST_DIE_CUT', 'Mindestabstand zu Schnittkante');
define('ZLP_CAD_F_SCANNING_MARK', 'Steuer-/Tastmarke');
define('ZLP_CAD_F_SIZE', 'Größe');
define('ZLP_CAD_F_COLOR', 'Farbe');
define('ZLP_CAD_F_WHITE_UNDERLAY', 'Weissunterlegung');
define('ZLP_CAD_F_KEY_MARK', 'Stand Tastmarke');
define('ZLP_CAD_F_POSITION', 'Position');
define('ZLP_CAD_F_DIMENSIONS', 'Maße nach Konfektionierung Breite x Höhe');
define('ZLP_CAD_F_MATERIAL_WIDTH', 'Materialbreite');
define('ZLP_CAD_F_FOIL_CUT_WIDTH', 'Folie Schnittbreite');
//define('ZLP_CAD_F_NOTES', 'Hinweise');

//ColourRetouching
define('ZLP_BB_COL_PROFILE', 'Farbprofil');
define('ZLP_BB_REF_JOB', 'Referenzauftrag');
define('ZLP_BB_COL_SEPARATION', 'Farbseparation');
define('ZLP_BB_VERSION', 'Version');

define('BLACK_SEPARATION', 'Schwarz-Aufbau');
define('ZLP_BB_SKELET_BLACK', 'Tiefe als Gerippe');
define('ZLP_BB_TRAVEL', 'Durchlaufend');

define('COLOR_SETUP', 'Farb-Aufbau');
define('ZLP_BB_CMY_CUT', 'CMY mit Abrissen');
define('ZLP_BB_CMY_TRAV', 'CMY Durchlaufend');

define('TONAL_VALUE', 'Tonwerte');
//define('ZLP_BB_MIN_DOT', 'kleinste Punktgröße in Datei');
//define('ZLP_REP_F_MAX_TON_VAL', 'max. Tonwert im Verlauf');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'max. Farbdeckung');
define('ZLP_BB_TEMPLATE', 'Vorlage/Referenz');

define('3D', '3D');
define('ZLP_BB_3D_IMG', '3D Image/Packshot');
define('ZLP_BB_NEW_VIEW', 'Neue Ansicht');
define('ZLP_BB_ADAPT', 'Adaption');
define('ZLP_BB_ADAPT_FROM', 'Adaption von');

define('ZLP_BB_MEMO_TXT', 'Sonstiges');

//Data Delivery
define('ZLP_DD_REF_JOB_NR', 'Referenzjobnr.');
define('ZLP_DD_DATA_SOURCE', 'Quelldaten');
define('ZLP_DD_FINAL_FILE', 'Ausgabeformat');
define('ZLP_DD_FILE_TRANSFER', 'Datenversandart');
define('ZLP_DD_ADDRESS', 'Adresse');

//Data Output
define('ZLP_DO_PRINTING_TYPE', 'Druckverfahren');
define('ZLP_DD_NOTES', 'Sonstiges');
//define('ZLP_AG_F_COLOR', 'Farben');
define('ZLP_DO_PLATE_SLEEVE_01', 'Platte/Sleeve');
define('ZLP_DO_DATATYPE_01', 'Datentyp');
define('ZLP_DO_RESOLUTION_01', 'Auflösung');
define('ZLP_DO_DISTORTION_01', 'Verzerrung');
define('ZLP_DO_DGC_01', 'DGC');
define('ZLP_GES_F_END_TO_END_01', 'Platten auf Stoß');
define('ZLP_GES_F_STAG_CUT_01', 'Stag. cut');
define('ZLP_DO_PROD_PLANT_01', 'Produktionsstandort');
define('ZLP_DO_LENFILE_NAME_01', 'LEN File Name');

//Flexo Plate
define('ZLP_FP_NO_DIGITAL_PLATES', 'Plattenanzahl (digital)');
define('ZLP_FP_SIZE_01', 'Plattengröße 1');
define('ZLP_FP_NO_ANALOG_PLATES', 'Plattenanzahl (analog)');
define('ZLP_FP_SIZE_02', 'Plattengröße 2');
define('ZLP_FP_FILM_EXPOSURE', 'Filmbelichtung');
define('ZLP_FP_SIZE_03', 'Plattengröße 3');
define('ZLP_FP_PLATE_CATEGORY', 'Art');
//define('ZLP_AG_F_COLOR_01', 'Farben');
define('ZLP_FP_PLATETYPE_01', 'Plattentyp');
define('ZLP_FP_MIN_DOT_HELD_01', 'kleinster Punkt auf Platte');
define('ZLP_FP_DIGI_CAP_01', 'Digicap');
define('ZLP_FP_NO_OF_PLATES_01', 'Nr.');
define('ZLP_FP_ROTATION_01', 'Rot. 90°');
define('ZLP_FP_OUTPUT_01', 'Ausgabe');
define('ZLP_FP_FLAT_TOP_01', 'Flat Top');
define('ZLP_FP_DIGITAL_ANALOGUE_01', 'Digital / Analog');
define('ZLP_FP_REMARKS_01', 'Anmerkung');

//Proof
//define('ZLP_BB_COL_PROFILE', 'Farbanpassung');
define('ZLP_PROOF_PROCESS_11', 'Proofverfahren');
define('ZLP_PROOF_NOTES_11', 'Hinweise');
define('ZLP_PROOF_SHIP_ADDRESS_11', 'Versand Adresse');
define('ZLP_PROOF_SIZE_11', 'Proofgröße');
define('ZLP_PROOF_LEGEND_11', 'Druckkarten Kopf');
define('ZLP_PROOF_PROFILE_NAME_11', 'Profil Name');
define('ZLP_PROOF_PROFILE_SUBSTRATE_11', 'Alternate Proof Substrate');
define('ZLP_PROOF_SHIPPING_11', 'Versand');
define('ZLP_PROOF_NUMBER_11', 'Proofanzahl');
define('ZLP_PROOF_1UP_11', 'Nutzen');
define('ZLP_PROOF_PROD_PLANT_11', 'Produktionsstandort');

//Mounting
define('ZLP_MT_MOUNTING_TYPE', 'Montageart');
define('ZLP_MT_PRESS_PROOF', 'Andruck');
define('ZLP_MT_PRESS_PROOF_TYPE', 'Andruckart');
define('ZLP_MT_PLOTTING_ON', 'Plott auf');
define('ZLP_MT_MOUNTINGS_NO', 'Anzahl der Montagen');
define('ZLP_MT_FOILS_HARD_NO', 'Anzahl der Folien hart');
define('ZLP_MT_FOILS_FOAM_NO', 'Anzahl der Folien auf Schaum');
define('ZLP_MT_CLICHES_NOT_MOUNTED', 'Klischeeanzahl unmontiert');
define('ZLP_MT_REMARKS_01', 'Hinweise/Sonstiges');
define('ZLP_MT_TOTAL_CLICHES_SET_UP', 'Gesamtklischeeaufbau');
define('ZLP_MT_CLICHE_THICKNESS', 'Klischeestärke');
define('ZLP_MT_MOUNTING_FOIL', 'Montagefolie');
define('ZLP_MT_ADHESIVE_FOIL', 'Klebefolie');
define('ZLP_MT_FOAM', 'Schaum');
define('ZLP_MT_REAL_CLICHE_HEIGHT', 'Tatsächliche Klischeehöhe');
define('ZLP_MT_RELIEF_DEPTH', 'Relieftiefe');
define('ZLP_MT_DISTORTION', 'Dehnung');
define('ZLP_MT_FILM_HEIGHT_FIX', 'Feste Folienhöhe');

define('GENERAL_SPECIFICATION', 'General Specification');
define('MOUNTING_FOIL', 'Montagefolie');
define('ZLP_MT_FOIL_WIDTH_FIX', 'Folienbreite fest');
define('ZLP_MT_FOIL_WIDTH_MIN', 'Folienbreite minimal');
define('ZLP_MT_FOIL_WIDTH_MAX', 'Folienbreite maximal');
define('ZLP_MT_FOIL_HEIGHT_FIX', 'Folienhöhe fest');
define('ZLP_MT_FOIL_HEIGHT_MIN', 'Folienhöhe minimal');
define('ZLP_MT_FOIL_HEIGHT_MAX', 'Folienhöhe maximal');
define('ZLP_MT_FOIL_CUT_FROM', 'Folie trennen ab');
define('ZLP_MT_GALLEY_PROOF_MOUNTING', 'Fahnnemontage');

define('MOUNTING_SLAT', 'Montageleiste');
define('ZLP_MT_CLIP_SYSTEM', 'Einhängesystem');
define('ZLP_MT_SLAT_TYPE_FRONT', 'Leistenart vorne');
define('ZLP_MT_SLAT_TYPE_BACK', 'Leistenart hinten');
define('ZLP_MT_FOIL_ARRANGEMENT', 'Folienanordnung');
define('ZLP_MT_CENTERLINE', 'Mittenzentrierung');
define('ZLP_MT_SLAT_FIXATION', 'Leiste Befestigung');

define('TEXTILE_TAPE', 'Gewebeband');
define('ZLP_MT_TEXTILE_TAPE_COLOR', 'Gewebeband Farbe');
define('ZLP_MT_TEXTILE_TAPE_WIDTH', 'Gewebeband Breite');
define('ZLP_MT_TEXTILE_TAPE_STAND', 'Gewebeband Stand');

define('MOUNTING_FOIL_CONFIGURATION', 'Montagefolie-Aufbau');
define('ZLP_MT_DIST_SLAT_TO_CUT', 'Abstand Leiste bis Zuschnitt');
define('ZLP_MT_DIST_FOIL_TO_CUT', 'Abstand Folienende bis Zuschnitt');
define('ZLP_MT_DIST_LEFT_RIGHT_TO_CUT', 'Abst. links+rechts b Zuschnitt');
define('ZLP_MT_DIST_SLAT_TO_PRINT', 'Abstand Leiste bis Druckanfang');
define('ZLP_MT_GLUEING_EDGE', 'Gluerand');

define('PULL_BANDS', 'Führungsstreifen');
//define('ZLP_GES_F_ALIGNMENT_BAR', 'Führungsstreifen');
define('ZLP_MT_PULL_BANDS_WIDTH', 'Führungsstreifen Breite');
define('ZLP_MT_PULL_BANDS_THICKNESS', 'Führungsstreifen Stärke');
//define('ZLP_GES_F_ALIGNMENT_BAR_POS', 'Führungsstreifen Position');
define('ZLP_MT_PULL_BANDS_TYPE', 'Führungssteifen Typ');
//define('ZLP_GES_F_PRINT_CTRL_ELEMENTS', 'Druckkontrollelemente');
define('ZLP_MT_PULL_BANDS_PRINTCTRL_02', 'Druckkontrollelemente 2');
define('ZLP_MT_PULL_BANDS_PRINTCTRL_03', 'Druckkontrollelemente 3');

define('EYES_RIVETS', 'Ösen und Nieten');
define('ZLP_MT_EYES', 'Ösen (hinten)');
define('ZLP_MT_EYES_DISTANCE', 'Ösenabstand');
define('ZLP_MT_EYES_SIZE', 'Ösengröße');
define('ZLP_MT_EYES_DIST_FROM_FOIL', 'Ösenabstand von Folie');
define('ZLP_MT_EYES_FOR_ARCHIVING', 'Ösen zum archivieren');
define('ZLP_MT_EYES_DISTANCE_ARCHIVE', 'Ösenabstand Archiv');
define('ZLP_MT_EYES_SIZE_ARCHIVE', 'Ösengröße Archiv');
define('ZLP_MT_EYES_DIST_FOIL_ARCHIVE', 'Ösenabstand Archiv von Folie');
define('ZLP_MT_RIVET', 'Nieten (hinten)');
define('ZLP_MT_RIVET_DISTANCE', 'Nietenabstand');
define('ZLP_MT_RIVET_DISTANCE_WIDTH', 'Nietenhöhe');
define('ZLP_MT_RIVET_DIST_FROM_FOIL', 'Nietenabstand von Folie');
define('ZLP_MT_DRAW_ORIGIN', 'Nullpunkt anzeichnen');
define('ZLP_MT_PACKAGING_TYPE', 'Verpackungsart');
define('ZLP_MT_REMARKS_02', 'Hinweise/Sonstiges');

define('ZLP_AG_F_COLOR_01', 'Farbbezeichnung');
define('ZLP_AG_F_PLATE_TYPE_01', 'Plattentyp');
define('ZLP_AG_F_PLATE_THICKNESS_01', 'Plattenstärke');
define('ZLP_AG_F_SPECIAL_RELIEF_01', 'spez. Relief');
define('ZLP_AG_F_CLICHE_NUMBER_01', 'Klischeenummer');
define('ZLP_AG_F_LINE_SCREEN_01', 'Strich/Raster');
define('ZLP_AG_F_ANGLE_01', 'Winkel');
define('ZLP_AG_F_CONT_SCR_CT_01', 'Halbton Rasterweite');
define('ZLP_AG_F_TECH_SCR_CT_01', 'techn. Rasterweite');
define('ZLP_AG_F_DOT_TYPE_01', 'Punktform');
define('ZLP_AG_F_DISTORTION_L_01', 'Verzerrungfaktor LR');
define('ZLP_AG_F_DISTORTION_C_01', 'Verzerrungfaktor QR');
define('ZLP_AG_F_REMARK_01', 'Bemerkungen');
define('ZLP_AG_F_PATCH_01', 'Patch');
define('ZLP_AG_F_INK_TYPE_01', 'Farbtyp');

//GenSpec Flexibles
define('ZLP_AG_F_SUBSTRATE', 'Bedruckstoff');
define('ZLP_AG_F_PRINT_METHOD', 'Druckart');
define('ZLP_AG_F_CYLINDER', 'Zylinderumfang');
define('ZLP_AG_F_REPEAT_LENGTH', 'Rapportlänge');
define('ZLP_AG_F_EXPANSION', 'Dehnungsfaktor');
define('ZLP_AG_F_ANGLE_TYPE', 'Angle Type');
define('CUSTOMER_SPECIFIC_FIELDS', 'Customer Specific Fields');
define('ZLP_AG_NOTES', 'Sonstiges');
define('ZLP_AG_BUSINESS_CATEGORY', 'Business Category');
define('ZLP_WEBCENTER_ID', 'WebCenter ID');
define('ZLP_DO_DGC', 'Plate DGC');
define('ZLP_AG_REF_JOB', 'Previous Job');

//define('COLOURS', 'Farben');
//define('ZLP_ANZF_VS', 'Anzahl Farben Job');
//define('ZLP_AG_F_MACHINE_COLORS', 'Max. Farbanzahl Maschine');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'max. Farbdeckung');
//define('ZLP_AG_F_COLOR_01', 'Farbbezeichnung');
//define('ZLP_AG_F_PLATE_TYPE_01', 'Plattentyp');
//define('ZLP_AG_F_PLATE_TYPE_HELP', 'Plattentyp Hilfstabelle');
//define('ZLP_AG_F_PLATE_THICKNESS_01', 'Plattenstärke');
//define('ZLP_AG_F_SPECIAL_RELIEF_01', 'spez. Relief');
//define('ZLP_AG_F_CLICHE_NUMBER_01', 'Klischeenummer');
//define('ZLP_AG_F_LINE_SCREEN_01', 'Strich/Raster');
//define('ZLP_AG_F_ANGLE_01', 'Winkel');
//define('ZLP_AG_F_CONT_SCR_CT_01', 'Halbton Rasterweite');
//define('ZLP_AG_F_TECH_SCR_CT_01', 'techn. Rasterweite');
//define('ZLP_AG_F_DOT_TYPE_01', 'Punktform');
//define('ZLP_AG_F_DISTORTION_L_01', 'Verzerrungfaktor LR');
//define('ZLP_AG_F_DISTORTION_C_01', 'Verzerrungfaktor QR');
//define('ZLP_AG_F_REMARK_01', 'Bemerkungen');
//define('ZLP_AG_F_PATCH_01', 'Patch');

//Repro Flexibles
//define('TRAPPING', 'Trapping');
//define('ZLP_REP_F_MIN_TRAP', 'Minimale Überfüllung');
//define('ZLP_REP_F_MAX_TRAP', 'Maximale Überfullung');
//define('ZLP_REP_F_STD_TRAP', 'Standard Überfüllung');
define('ZLP_REP_F_COL_TRAP', 'Metallic Farben Überfüllung');
define('ZLP_REP_F_WHITE_TRAP_01', 'Weiß Überfüllung 1');
define('ZLP_REP_F_WHITE_TRAP_02', 'Weiß Überfüllung 2');
define('ZLP_REP_F_WHITE_PULL_01', 'Weißeinzug 1');
define('ZLP_REP_F_WHITE_PULL_02', 'Weißeinzug 2');
//define('ZLP_REP_F_VARNISH_TRAP', 'Lack Überfüllung');
define('ZLP_REP_F_VARNISH_PULL', 'Lackeinzug');

//define('ARTWORK_INFORMATION', 'Motivinformation');
//define('ZLP_REP_F_MIN_LINE_POS', 'min. Linienstärke pos.');
//define('ZLP_REP_F_MIN_LINE_NEG', 'min. Linienstärke neg. ');
//define('ZLP_REP_F_MIN_TXT_SIZE_POS', 'min. Textgröße pos.');
//define('ZLP_REP_F_MIN_TXT_SIZE_NEG', 'min. Textgröße neg.');
//define('ZLP_BB_MIN_DOT', 'Kleinste Punktgröße in Datei');
//define('ZLP_REP_F_MAX_TON_VAL', 'max. Tonwert im Verlauf');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'max. Farbdeckung');
define('ZLP_REP_F_ORIENTATION', 'Laufrichtung zum Motiv');
//define('ZLP_CAD_F_PRINT_HEIGHT', 'Druckbildhöhe');
//define('ZLP_CAD_F_PRINT_WIDTH', 'Druckbildbreite');
//define('ZLP_CAD_F_DISTANCE_LEFT', 'Abstand links');
//define('ZLP_CAD_F_DISTANCE_RIGHT', 'Abstand rechts');
//define('ZLP_CAD_F_DISTANCE_TOP', 'Abstand oben');
//define('ZLP_CAD_F_DISTANCE_BOTTOM', 'Abstand unten');
//define('ZLP_CAD_F_MIN_DIST_DIE_CUT', 'Mindestabstand zu Schnittkante');
define('ZLP_CAD_F_PRINT_HEIGHT_TXT', 'Print area overall length');
define('ZLP_CAD_F_PRINT_WIDTH_TXT', 'Print area overall width');

define('ZLP_REP_F_CAP_TOP', 'Überdruck Umschlag/Klappe');
define('ZLP_REP_F_BLEED', 'Überdruck Beschnitt');
define('ZLP_REP_F_BOTTOM', 'Überdruck Bodenfalte');
define('ZLP_REP_F_SIDE_FRAMES', 'Überdruck Seitenfalte');
//define('ZLP_REP_C_NOTES', 'Hinweise/Sonstiges');

//StepRepeat Flexibles
//define('ZLP_CAD_F_DIE_CUT_NO', 'CAD Nummer');
define('ZLP_GES_F_MULTIUP', 'Gesamtform vom Kunden');
define('ZLP_GES_F_1UP_RADIAL', 'Nutzenanzahl Radial');
define('ZLP_GES_F_RAP_RADIAL', 'Rapport Radial');
define('ZLP_GES_F_STAG_RADIAL', 'Versatz Radial');
define('ZLP_GES_F_MOUNTED_01', 'montiert Radial');
define('ZLP_GES_F_1UP_AXIAL', 'Nutzenanzahl Axial');
define('ZLP_GES_F_RAP_AXIAL', 'Rapport Axial');
define('ZLP_GES_F_STAG_AXIAL', 'Versatz Axial');
define('ZLP_GES_F_MOUNTED_02', 'montiert Axial');
define('ZLP_GES_F_1UP_TOTAL', 'Gesamtnutzenanzahl');
define('ZLP_GES_F_SCAT_PRINT', 'Streudruck');
define('ZLP_GES_F_OVERALL_LENGTH', 'Gesamtlänge');
define('ZLP_GES_F_OVERALL_WIDTH', 'Gesamtbreite');
//define('ZLP_GES_F_REG_MARK_TYPE', 'Passer Typ/Größe');
//define('ZLP_GES_F_REG_MARK_POS', 'Passer Position');
//define('ZLP_GES_F_ALIGNMENT_BAR', 'Führungsstreifen');
//define('ZLP_GES_F_ALIGNMENT_BAR_POS', 'Führungsstreifen Position');
//define('ZLP_GES_F_PRINT_CTRL_ELEMENTS', 'Druckkontrollelemente');
define('ZLP_GES_F_1UP_MARK', 'Nutzen Kennzeichnung');
//define('ZLP_CAD_F_SCANNING_MARK', 'Steuer-/Tastmarke');
//define('ZLP_CAD_F_SIZE', 'Größe');
//define('ZLP_CAD_F_COLOR', 'Farbe');
//define('ZLP_CAD_F_WHITE_UNDERLAY', 'Weissunterlegung');
//define('ZLP_CAD_F_KEY_MARK', 'Stand Tastmarke');
//define('ZLP_CAD_F_POSITION', 'Position ');
define('ZLP_GES_F_NOTES', 'Sonstiges');
define('ZLP_AG_F_COLOR_1', 'Farbbezeichnung');
define('ZLP_GES_F_END_TO_END_1', 'Stoßverlegung');
define('ZLP_GES_F_STAG_CUT_1', 'Versetzter Schnitt');
define('ZLP_CAD_F_TOTAL_PRINT_WIDTH', 'Gesamtdruckbreite');
define('ZLP_GES_F_LAYOUT_POS', 'Motivanordnung');
define('ZLP_GES_F_OFFSET', 'Versatz');
define('ZLP_GES_GAP_F_CIRCUMFERENCE', 'Stoß');
define('ZLP_GES_F_ALIGNMENT_BAR_SIZE', 'Führungsstreifen Breite');
define('ZLP_GES_F_ALIGNMENT_BAR_COLOR', 'Führungsstreifen Farbe');

define('MOTIF_ARRANGEMENT', 'Motif arrangement');

//Gravure Cylinder
define('ZLP_GC_STAGGERING', 'Staffelung');
define('ZLP_GC_AMT_NEW', 'Anz. Neue Zylinder');
define('ZLP_GC_AMT_STOCK', 'Anz. vom Lager');
define('ZLP_GC_AMT_OLD', 'Anz. Alte Zylinder');
define('ZLP_GC_OLD_CYL', 'Altzylinder');
define('ZLP_GC_OLD_CYL_DATE', 'Altzylinder Datum');
define('ZLP_GC_MATERIAL', 'Material');
define('ZLP_GC_MATERIAL_DATE', 'Material Datum');
define('ZLP_GC_NOTES', 'Sonstiges');
define('ZLP_GC_USAGE_01', 'Verwendung');
define('ZLP_GC_PROCESS_01', 'Verfahren');
define('ZLP_GC_NOMINALSIZE_01', 'Nummer');
define('ZLP_GC_CODE_01', 'Code');
define('ZLP_GC_PROOF_01', 'Andruck');
define('ZLP_AG_NEW_01', 'Neu');
define('ZLP_GC_SCREEN_DEPTH_01', 'Sticheltiefe');

//Offset Plate
//define('ZLP_AG_F_SUBSTRATE', 'Bedruckstoff');
define('ZLP_OF_GRAMMAGE', 'Grammatur');
define('ZLP_OF_GRIPPER', 'Greifer');
define('ZLP_OF_PULL_LAY', 'Ziehmarke');
define('ZLP_OF_LAY', 'Anlage');
define('ZLP_OF_VIEW', 'Ansicht');
define('ZLP_OF_TURN', 'Wendeart');
define('ZLP_OF_ORIGFORMAT', 'Originalformat');
define('ZLP_OF_PRINTFORMAT', 'Auflagenformat');
//define('ZLP_BB_COL_PROFILE', 'Farbprofil');
define('ZLP_OF_PLATE_SIZE', 'Plattenformat');
define('ZLP_OF_VARNISH', 'Lackarten');
define('ZLP_OF_BLANKET', 'Lacktucht');
define('ZLP_OF_BLANKET_OLD', 'Altes Lacktuch');
define('ZLP_OF_BLANKET_OLD_NO', 'DB-Nr. altes Lacktuch');
define('ZLP_OF_TOOL_NUMBER', 'Werkzeug Nummer');
define('ZLP_OF_DRAW_NUMBER', 'Zeichnungs Nummer');
define('ZLP_OF_MEMO', 'Sonstiges');

//Correction
define('CORRECTION_ON', 'Korrektur auf');
define('CORRECTION_PER', 'Korrektur per');
define('CORRECTION_DATE', 'Datum');
define('CORRECTION_NO', 'Korrektur Nr.');
define('CORRECTION_FROM', 'Korrektur von');
define('CORRECTION_COMPLETED', 'Fertiggestellt');

//Approval
define('APPROVAL_VIA', 'Freigabe per');
define('APPROVAL_TYPE', 'Freigabeart');
define('APPROVAL_DATE', 'Freigabe am');
define('APPROVAL_BY', 'Freigabe von');

//Production Art
define('ZLP_AW_BASED_ON', 'Artwork auf Basis');
define('ZLP_AW_ADAPTION', 'Adaption nach');
define('ZLP_AW_PURCHASE', 'Fotografie zukaufen');
define('ZLP_AW_COR_LAYOUT', 'Motiv-/Standänderung');
define('ZLP_AW_COR_TEXT', 'Texterstellung/-änderung');
define('ZLP_AW_COR_IMAGE', 'Farb-/Bildbearbeitung');
define('ZLP_AW_NOTES', 'Sonstiges');

//Creative
define('ZLP_CR_DESIGNTYPE', 'Designart');
define('ZLP_CR_PURCHASE', 'Fremdleistungen');
define('ZLP_CR_DESIGNS', 'Anzahl Entwürfe');
define('ZLP_CR_BRIEFINGDATE', 'Termin Briefing');
define('ZLP_CR_NOTES', 'Sonstiges');

//Photography
define('ZLP_PH_CONCEPT', 'Motivkonzept');
define('ZLP_PH_PICTURES', 'Anzahl Bilder');
define('ZLP_PH_BRIEFING', 'Vorgabe / Briefing');
define('ZLP_PH_REFJOB', 'Referenzauftrag');
define('ZLP_PH_DIRECTOR', 'Fotoregie vor Ort durch');
define('ZLP_PH_ELEMENTS', 'Bildelemente');
define('ZLP_PH_NOTES', 'Sonstiges');

//Artistic Retouching
define('ZLP_AR_TEMPLATE1', 'Vorlage 1');
define('ZLP_AR_TEMPLATE2', 'Vorlage 2');
define('ZLP_AR_TEMPLATE3', 'Vorlage 3');
define('ZLP_AR_REFJOB', 'Vorauftrag');
define('ZLP_AR_MASK', 'Freistellmaske erstellen');
define('ZLP_AR_NOTES', 'Hinweise');

//Mockup
define('ZLP_MC_AMOUNT', 'Anzahl');
define('ZLP_MC_PURCHASING', 'Fremdleistung');
define('ZLP_MC_NOTES', 'Sonstiges');

//Technical Services
define('PROOF_PROFILE', 'Nachweisprofil');
define('ZLP_TS_PROFILE', 'Farbprofil basierend auf');
define('ZLP_TS_PROFILE_NAME', 'Neuer Profil name');
define('ZLP_TS_PROFILE_ID', 'Neue Profile Spec ID');
define('ZLP_TS_PROOF', 'Proofart');
define('ZLP_TS_REFERENCE', 'Referenz');
define('ZLP_TS_CMYK', 'Profilierung (CMYK)');
define('ZLP_TS_SPOT_COLORS', 'Sonderfarben');
define('ZLP_TS_DGC', 'DGC');
define('ZLP_TS_NOTES_01', 'Sonstiges');

define('PRINTING_APPROVAL', 'Druckgenehmigung');
define('ZLP_TS_INSTIGATION', 'Veranlassung');
define('ZLP_TS_CONTACT_PRINTER', 'Ansprechpartner Drucker');
define('ZLP_TS_CONTACT_CUSTOMER', 'Ansprechpatner Endkunde');
define('ZLP_TS_REF_ARTWORK', 'Referenzen Motiv');
define('ZLP_TS_REF_SPOT_COLORS', 'Referenzen Sonderfarben');
define('ZLP_TS_PRINT_ANALYSIS', 'Druckauswertung');
define('ZLP_TS_NOTES_02', 'Sonstiges');

//CDI Worksheet
define('CDI_WORKSHEET', 'Arbeitsblatt CDI');
define('CDI_WORKSHEET_FOR_ORDER', 'Arbeitsblatt CDI für Order');
define('DESCRIPTION', 'Motiv');
define('FINISH_DATE', 'Endtermin');
define('PERSONAL_NAME', 'Bearbeiter Esko');
define('FACE_PRINT', 'Schöndruck');
define('PRODUCTION_NO', 'Produktionsnr.');
define('BATCH_NO', 'Batch nr');
define('BOX_NO', 'Box nr');
define('TYPE', 'Typ');
define('CDI_IMAGING_OPERATOR', 'CDI-Bebilderung durch');
define('EXPOSURE_OPERATOR', 'Plattenbelichtung durch');
define('QUALITY_CONTROL', 'Endkontrolle durch');
define('DATE', 'Datum');

define('CDI_ZLP_MT_RELIEF_DEPTH', 'Relief');
define('CDI_ZLP_FP_NO_OF_PLATES_01', 'Anzahl');
define('CDI_ZLP_AG_F_PLATE_TYPE_01', 'Typ');
define('CDI_ZLP_FP_DIGITAL_ANALOGUE_01', 'D / A');
define('CDI_ZLP_AG_F_PLATE_THICKNESS_01', 'Stärke');
define('CDI_ZLP_GES_F_STAG_CUT_01', 'Staggered');

//Value translations

//usage type
define('New order', 'Neuauftrag');
define('Change order', 'Änderungsauftrag');
define('Repeat order', 'Wiederholauftrag');
define('Test order', 'Test Auftrag');
define('Free of charge', 'Ohne Berechnung');

//printing type
define('FACE PRINTING', 'Schöndruck');
define('REVERSE PRINTING', 'Konterdruck');