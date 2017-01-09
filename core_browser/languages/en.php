<?php
//General
define('SITE_NAME', 'Service Order Report');
define('ERROR_OCCURRED', 'An Error Occurred');
define('SERVICE_ORDER_SEARCH', 'Search for a service order');
define('SERVICE_ORDER_RECENT', 'Recently Visited Service Orders');

define('ORDER_DETAILS', 'Order Details');
define('SALES_ORDER', 'Sales Order');
define('SKU_MATERIAL_NUMBER', 'SKU Material Number');
define('CUSTOMER_MATERIAL_NUMBER', 'Customer Material Number');
define('CUSTOMER_DESCRIPTION', 'Customer Description');
define('PRINT_METHOD', 'Print Method');
define('PRINT_MACHINE', 'Print Machine');
define('DUE_DATE', 'Due Date');

define('CONTACTS', 'Contacts');
define('CONTACTS_AP', 'Contact person');
define('CONTACTS_ZM', 'Employee respons.');
define('CONTACTS_VE', 'Sales employee');
define('CONTACTS_WE', 'Ship-to party');
define('CONTACTS_AG', 'Sold-to party');

define('PARTNERS', 'Partners');
define('PARTNERS_ZN', 'Printer');
define('PARTNERS_ZO', 'Brandowner');
define('PARTNERS_ZR', 'Agency');
define('PARTNERS_ZS', 'Supplier');

define('OPERATIONS', 'Operations');
define('CONFIRMED_OPERATIONS', 'Confirmed Operations');
define('COLOR_ROTATION', 'Color Rotation Details');
define('DATE_FORMAT', 'Y-m-d');

//GenSpec Corrugated
define('PRINTING_MATERIAL', 'Printing material');
define('ZLP_AG_C_CORRUGATED_TYPE', 'Corrugated type');
define('ZLP_AG_C_SURFACE', 'Printing Surface');
define('ZLP_AG_C_SHEET_FORMAT', 'Sheet format');
define('ZLP_AG_C_PRINTSIZE_MAX_WIDTH', 'max. printsize width');
define('ZLP_AG_C_PRINTSIZE_MAX_HEIGHT', 'max. printsize height');
define('ZLP_AG_C_PRINTSIZE_MIN_WIDTH', 'min. printsize width');
define('ZLP_AG_C_PRINTSIZE_MIN_HEIGHT', 'min. printsize height');

define('COLOURS', 'Colours');
define('ZLP_ANZF_VS', 'Number of colors for job');
define('ZLP_AG_F_MACHINE_COLORS', 'Max. number of colors  for machine');
define('ZLP_REP_F_MAX_INK_COVERAGE', 'Max. Ink coverage');

define('ZLP_AG_F_COLOR', 'Color name');
define('ZLP_AG_F_PLATE_TYPE', 'Plate type');
define('ZLP_AG_F_PLATE_TYPE_HELP', 'Plate type helptable');
define('ZLP_AG_F_PLATE_THICKNESS', 'Plate thickness');
define('ZLP_AG_F_SPECIAL_RELIEF', 'spec. Relief');
define('ZLP_AG_F_CLICHE_NUMBER', 'cliche nummer');
define('ZLP_AG_F_LINE_SCREEN', 'line/screen');
define('ZLP_AG_F_ANGLE', 'Angle');
define('ZLP_AG_F_CONT_SCR_CT', 'con-tone screen count');
define('ZLP_AG_F_TECH_SCR_CT', 'techn. screen count');
define('ZLP_AG_F_DOT_TYPE', 'dot type');
define('ZLP_AG_F_DISTORTION_L', 'distortion lenghtwise');
define('ZLP_AG_F_DISTORTION_C', 'distortion crosswise');
define('ZLP_AG_F_REMARK', 'remark');
define('ZLP_AG_F_PATCH', 'patch');

//CAD Corrugated
define('ZLP_CAD_F_DATA_SOURCE', 'Data source');
define('ZLP_CAD_C_DIE_CUT_NO', 'Die Cut no.');
define('ZLP_CAD_C_CUTTING_NO', 'Cutting no.');
define('ZLP_CAD_C_TOOL_NO', 'Tool no.');
define('ZLP_CAD_C_PREV_DIE_CUT_NO', 'Previous die cut no.');
define('ZLP_CAD_C_PREV_JOB_NO', 'Previous job no.');
define('ZLP_CAD_F_CAD_VIEW', 'CAD View');
define('ZLP_CAD_F_IN_LINE_TO', 'in line to the');
define('ZLP_CAD_F_PASS_FILE', 'Pass File/CAD Register');
define('ZLP_CAD_C_3D_CAD', '3D CAD');
define('ZLP_CAD_C_CONSTRUCTION', 'Construction');
define('ZLP_CAD_C_ADHERING', 'Adhering');
define('ZLP_CAD_F_PRINT_VIEW', 'Print view');
define('ZLP_CAD_F_DIE_CUT_NAME', 'Die cut name');
define('ZLP_CAD_F_NOTES', 'Notes');

define('PROCESSING_SACK', 'Processing Sack');
define('ZLP_CAD_C_RS1', 'RS 1');
define('ZLP_CAD_C_SF_01', 'SF');
define('ZLP_CAD_C_VS', 'VS');
define('ZLP_CAD_C_SF_02', 'SF');
define('ZLP_CAD_C_RS2', 'RS 2');
define('ZLP_CAD_C_ADH_BACK', 'Adhering at the back side');
define('ZLP_CAD_C_ADH_SIDE', 'Adhering at the side');
define('ZLP_CAD_C_BOTTOM_HGT', 'Bottom height');

define('PROCESSING_WFK', 'Processing WFK');
define('ZLP_CAD_C_GLUE_LAP', 'Glue lap');
define('ZLP_CAD_C_SIDE_01', '1st side');
define('ZLP_CAD_C_SIDE_02', '2nd side');
define('ZLP_CAD_C_SIDE_03', '3rd side');
define('ZLP_CAD_C_SIDE_04', '4th side');
define('ZLP_CAD_C_HEIGHT', 'Height');
define('ZLP_CAD_C_LID', 'Lid');
define('ZLP_CAD_C_BOTTOM', 'Bottom');

//Repro Corrugated
//define('TRAPPING', 'Trapping');
define('ZLP_REP_F_MIN_TRAP', 'Minimum Trap');
define('ZLP_REP_F_MAX_TRAP', 'Maximum Trap');
define('ZLP_REP_F_STD_TRAP', 'Standard Trap');
define('ZLP_REP_F_VARNISH_TRAP', 'Varnish Trap');

define('ARTWORK_INFORMATION', 'Artwork Information');
define('ZLP_REP_F_MIN_LINE_POS', 'Min. line thickness pos.');
define('ZLP_REP_F_MIN_LINE_NEG', 'Min. line thickness rev.');
define('ZLP_REP_F_MIN_TXT_SIZE_POS', 'Min. text size pos.');
define('ZLP_REP_F_MIN_TXT_SIZE_NEG', 'Min. text size rev.');
define('ZLP_BB_MIN_DOT', 'Min. Dot Size %');
define('ZLP_REP_F_MAX_TON_VAL', 'Max. Tonal value in gradient');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'Max. Ink coverage');
define('ZLP_REP_C_RESY', 'Resy sign');
define('ZLP_REP_C_FSC_LOGO', 'FSC logo');
define('ZLP_REP_C_FSC_LOGO_VAR', 'FSC logo variant');

define('BLEED', 'Bleed');
define('ZLP_REP_C_GENERAL_BLEED', 'General bleed');
define('ZLP_REP_C_MIN_DIST_DIE_CUT', 'Min. Distance to Die-Cut');
define('ZLP_REP_C_BLEED_TRAP_LAP', 'Bleed/Trap in lap');
define('ZLP_REP_C_BLEED_TRAP_TOP_BOT', 'Bleed/Trap in Bottom/Top');
define('ZLP_REP_C_MIN_DIST_CREASING', 'Min. Distance to creasing');
define('ZLP_REP_C_NOTES', 'Notes/Others');

//StepRepeat Corrugated
//define('ZLP_CAD_C_DIE_CUT_NO', 'CAD number');
define('ZLP_GES_C_MULTIUP', 'Multiup layout from customer');
define('ZLP_GES_C_1UP_RADIAL', 'Number of one-ups radial');
define('ZLP_GES_C_RAP_RADIAL', 'Rapport radial');
define('ZLP_GES_C_STAG_RADIAL', 'Staggering radial');
define('ZLP_GES_C_1UP_AXIAL', 'Number of one-ups axial');
define('ZLP_GES_C_RAP_AXIAL', 'Rapport Axial');
define('ZLP_GES_C_STAG_AXIAL', 'Staggering Axial');
define('ZLP_GES_C_1UP_TOTAL', 'Total amount of one-ups');
define('ZLP_GES_F_REG_MARK_TYPE', 'Registration mark type/size');
define('ZLP_GES_F_REG_MARK_POS', 'Registration mark position');
define('ZLP_GES_F_ALIGNMENT_BAR', 'Alignment bar');
define('ZLP_GES_F_ALIGNMENT_BAR_POS', 'Alignment bar position');
define('ZLP_GES_F_PRINT_CTRL_ELEMENTS', 'Print control elements');
define('ZLP_GES_C_1UP_MARK', 'One-up Mark');
define('ZLP_GES_C_NOTES', 'Notes/other');

//Barcode
define('ZLP_BC_BWR', 'BWR');
define('ZLP_BC_CODE_TYPE', 'Code type');
define('ZLP_BC_CODE_COLOR', 'Code colour');
define('ZLP_BC_CODE_NUMBER', 'Code number');
define('ZLP_BC_BCKGRND_COLOR', 'Background colour');
define('ZLP_BC_TEST_PROTOCOL', 'Test protocol');
define('ZLP_BC_MAGNIFICATION', 'Magnification');
define('ZLP_BC_RESOLUTION', 'Resolution');
define('ZLP_BC_NARROW_BAR', 'Narrow bar');
define('ZLP_BC_RATIO', 'Ratio');
define('ZLP_BC_DEVICE_COMPENSATION', 'Device Compensation');
define('ZLP_BC_CODE_SIZE', 'Code size');
define('ZLP_BC_SUB_TYPE', 'Datamatrix Sub Type');

//CSR
define('CUSTOMER_FILES', 'Customer files');
define('ZLP_AV_REF_JOB_NR', 'Reference job number');
define('ZLP_AV_AMOUNT', 'Amount');
define('ZLP_AV_DATA_SOURCE', 'Source data');
define('ZLP_AV_CONVERT_TO', 'Convert to');
define('ZLP_AV_CUST_HARDCOPY_NO', 'Number of customer hardcopys');
define('ZLP_AV_CUST_HARDCOPY_SIZE', 'Customer hardcopy size/format');
define('ZLP_AV_KEEP_LIVE_TEXT', 'Keep live text for corrections');
define('ZLP_AV_PDF_UPLOAD', 'Upload reference PDF');
define('ZLP_AV_NOTES_01', 'Notes/other');
define('ZLP_AV_RULES_GUIDELINES', 'Rules and guidelines');
define('ZLP_AV_DATA_ENTRY_CONFORM', 'Data entry conform');
define('ZLP_AV_DATA_HANDLED_CONFORM', 'Data handled conform');

define('TEXT_EDITING', 'Text editing');
define('ZLP_AV_TEMPLATE', 'Template');

define('REFERENCE_FOR', 'Reference for');
define('ZLP_AV_TEXT', 'Text');
define('ZLP_AV_POSITION', 'Position');
define('ZLP_AV_FONT_SIZE', 'Font/Font size');
define('ZLP_AV_NOTES_02', 'notes/other');

//CAD Flexibles
//define('ZLP_CAD_F_DATA_SOURCE', 'Data source');
define('ZLP_CAD_F_DIE_CUT_NO', 'Die Cut no.');
define('ZLP_CAD_F_CUTTING_NO', 'Cutting no.');
define('ZLP_CAD_F_TOOL_NO', 'Tool no.');
define('ZLP_CAD_F_PREV_DIE_CUT_NO', 'Previous die cut no.');
define('ZLP_CAD_F_PREV_JOB_NO', 'Previous job no.');
//define('ZLP_CAD_F_CAD_VIEW', 'CAD View');
//define('ZLP_CAD_F_IN_LINE_TO', 'in line to the');
//define('ZLP_CAD_F_PASS_FILE', 'Pass File/CAD Register');
define('ZLP_CAD_F_3D_CAD', '3D CAD');
define('ZLP_CAD_F_CONSTRUCTION', 'Construction');
define('ZLP_CAD_F_ADHERING', 'Adhering');
//define('ZLP_CAD_F_PRINT_VIEW', 'Print view');

define('PROCESSING_FLEXIBLE_BAGS', 'Processing Flexible Bag');
define('PROCESSING_FILM_BAGS', 'Processing film bags');
define('ZLP_CAD_F_BAG_LENGTH', 'Foil bag length');
define('ZLP_CAD_F_BAG_WIDTH', 'Foil bag width');
define('ZLP_CAD_F_BOTTOM_CREASE', 'Bottom crease');
define('ZLP_CAD_F_FLAP', 'Flap');

define('PROCESSING_FLAT_FOIL', 'Processing Flat Foil');
define('PROCESSING_FLAT_FILM', 'Processing flat film');
define('ZLP_CAD_F_LENGTH', 'Length');
define('ZLP_CAD_F_WIDTH', 'Width');
define('ZLP_CAD_F_PRINT_HEIGHT', 'Print area size height');
define('ZLP_CAD_F_PRINT_WIDTH', 'Print area size width');
define('ZLP_CAD_F_DISTANCE_LEFT', 'Distance left');
define('ZLP_CAD_F_DISTANCE_RIGHT', 'Distance right');
define('ZLP_CAD_F_DISTANCE_TOP', 'Distance top');
define('ZLP_CAD_F_DISTANCE_BOTTOM', 'Distance bottom');
define('ZLP_CAD_F_MIN_DIST_DIE_CUT', 'Min. Distance to Die-cut');
define('ZLP_CAD_F_SCANNING_MARK', 'Eye mark');
define('ZLP_CAD_F_SIZE', 'Size');
define('ZLP_CAD_F_COLOR', 'Color');
define('ZLP_CAD_F_WHITE_UNDERLAY', 'White underlay');
define('ZLP_CAD_F_KEY_MARK', 'Key mark');
define('ZLP_CAD_F_POSITION', 'Position');
define('ZLP_CAD_F_DIMENSIONS', 'Dimensions after packing w x h');
define('ZLP_CAD_F_MATERIAL_WIDTH', 'Material width');
define('ZLP_CAD_F_FOIL_CUT_WIDTH', 'Foil cutting width');
//define('ZLP_CAD_F_NOTES', 'Notes');

//ColourRetouching
define('ZLP_BB_COL_PROFILE', 'Job Color profile');
define('ZLP_BB_REF_JOB', 'Reference job');
define('ZLP_BB_COL_SEPARATION', 'Color separation');
define('ZLP_BB_VERSION', 'Version');

define('BLACK_SEPARATION', 'Black separation');
define('ZLP_BB_SKELET_BLACK', 'Skelet Black');
define('ZLP_BB_TRAVEL', 'Traveling');

define('COLOR_SETUP', 'Color Set-up');
define('ZLP_BB_CMY_CUT', 'CMY cutted');
define('ZLP_BB_CMY_TRAV', 'CMY traveling');

define('TONAL_VALUE', 'Tonal value');
//define('ZLP_BB_MIN_DOT', 'min. Dot %');
//define('ZLP_REP_F_MAX_TON_VAL', 'Max. Tonal value in gradient');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'Max. Ink coverage');
define('ZLP_BB_TEMPLATE', 'Template/Reference');

define('3D', '3D');
define('ZLP_BB_3D_IMG', '3D Image/Packshot');
define('ZLP_BB_NEW_VIEW', 'New View');
define('ZLP_BB_ADAPT', 'Adaptation');
define('ZLP_BB_ADAPT_FROM', 'Adaptation from');

define('ZLP_BB_MEMO_TXT', 'Notes');

//Data Delivery
define('ZLP_DD_REF_JOB_NR', 'Reference job number');
define('ZLP_DD_DATA_SOURCE', 'Source data');
define('ZLP_DD_FINAL_FILE', 'Final file format');
define('ZLP_DD_FILE_TRANSFER', 'File transfer');
define('ZLP_DD_ADDRESS', 'Address');

//Data Output
define('ZLP_DO_PRINTING_TYPE', 'Printing Type');
define('ZLP_DD_NOTES', 'Notes/other');
//define('ZLP_AG_F_COLOR', 'Separation/color');
define('ZLP_DO_PLATE_SLEEVE_01', 'Plate/Sleeve');
define('ZLP_DO_DATATYPE_01', 'Data Type');
define('ZLP_DO_RESOLUTION_01', 'Resolution');
define('ZLP_DO_DISTORTION_01', 'Distortion');
define('ZLP_DO_DGC_01', 'Plate DGC');
define('ZLP_GES_F_END_TO_END_01', 'End-to-end laying');
define('ZLP_GES_F_STAG_CUT_01', 'Stag. cut');
define('ZLP_DO_PROD_PLANT_01', 'Production plant');
define('ZLP_DO_LENFILE_NAME_01', 'LEN File Name');

//Flexo Plate
define('ZLP_FP_NO_DIGITAL_PLATES', 'Total no. of plates (digital)');
define('ZLP_FP_SIZE_01', 'Plate size 1');
define('ZLP_FP_NO_ANALOG_PLATES', 'Total number of plates (analog)');
define('ZLP_FP_SIZE_02', 'Plate size 2');
define('ZLP_FP_FILM_EXPOSURE', 'Film exposure');
define('ZLP_FP_SIZE_03', 'Plate size 3');
define('ZLP_FP_PLATE_CATEGORY', 'Plate category');
//define('ZLP_AG_F_COLOR_01', 'Color name');
define('ZLP_FP_PLATETYPE_01', 'Plate type');
define('ZLP_FP_MIN_DOT_HELD_01', 'Min Dot Held');
define('ZLP_FP_DIGI_CAP_01', 'DigiCap');
define('ZLP_FP_NO_OF_PLATES_01', 'No.');
define('ZLP_FP_ROTATION_01', 'Rot. 90°');
define('ZLP_FP_OUTPUT_01', 'Output');
define('ZLP_FP_FLAT_TOP_01', 'Flat Top');
define('ZLP_FP_DIGITAL_ANALOGUE_01', 'Digital Analogue');
define('ZLP_FP_REMARKS_01', 'Remarks');

//Proof
//define('ZLP_BB_COL_PROFILE', 'Standard profile');
define('ZLP_PROOF_PROCESS_11', 'Proof process');
define('ZLP_PROOF_NOTES_11', 'Notes');
define('ZLP_PROOF_SHIP_ADDRESS_11', 'Shipping address');
define('ZLP_PROOF_SIZE_11', 'Proof size/Format');
define('ZLP_PROOF_LEGEND_11', 'Legend');
define('ZLP_PROOF_PROFILE_NAME_11', 'Alternate Proof Profile');
define('ZLP_PROOF_PROFILE_SUBSTRATE_11', 'Alternate Proof Substrate');
define('ZLP_PROOF_SHIPPING_11', 'Proof Shipping');
define('ZLP_PROOF_NUMBER_11', 'Number of proofs');
define('ZLP_PROOF_1UP_11', 'One-up');
define('ZLP_PROOF_PROD_PLANT_11', 'Production plant');

//Mounting
define('ZLP_MT_MOUNTING_TYPE', 'Mounting type');
define('ZLP_MT_PRESS_PROOF', 'Press proof');
define('ZLP_MT_PRESS_PROOF_TYPE', 'Press proof type');
define('ZLP_MT_PLOTTING_ON', 'Plotting on');
define('ZLP_MT_MOUNTINGS_NO', 'Number of mountings');
define('ZLP_MT_FOILS_HARD_NO', 'Number of foils hard');
define('ZLP_MT_FOILS_FOAM_NO', 'Number of foils on foam');
define('ZLP_MT_CLICHES_NOT_MOUNTED', 'No. of clichés not mounted');
define('ZLP_MT_REMARKS_01', 'Remarks');
define('ZLP_MT_TOTAL_CLICHES_SET_UP', 'Total clichés set-up');
define('ZLP_MT_CLICHE_THICKNESS', 'Cliché thickness');
define('ZLP_MT_MOUNTING_FOIL', 'Mounting foil');
define('ZLP_MT_ADHESIVE_FOIL', 'Adhesive foil');
define('ZLP_MT_FOAM', 'Foam');
define('ZLP_MT_REAL_CLICHE_HEIGHT', 'Real cliché height');
define('ZLP_MT_RELIEF_DEPTH', 'Relief depth');
define('ZLP_MT_DISTORTION', 'Distortion');
define('ZLP_MT_FILM_HEIGHT_FIX', 'Film height fixed');

define('GENERAL_SPECIFICATION', 'General Specification');
define('MOUNTING_FOIL', 'Mounting foil');
define('ZLP_MT_FOIL_WIDTH_FIX', 'Fixed foil width');
define('ZLP_MT_FOIL_WIDTH_MIN', 'Minimum foil width');
define('ZLP_MT_FOIL_WIDTH_MAX', 'Maximum foil width');
define('ZLP_MT_FOIL_HEIGHT_FIX', 'Fixed foil height');
define('ZLP_MT_FOIL_HEIGHT_MIN', 'Minimum foil height');
define('ZLP_MT_FOIL_HEIGHT_MAX', 'Maximum foil height');
define('ZLP_MT_FOIL_CUT_FROM', 'Cut foil from');
define('ZLP_MT_GALLEY_PROOF_MOUNTING', 'Galley proof mounting');

define('MOUNTING_SLAT', 'Mounting slat');
define('ZLP_MT_CLIP_SYSTEM', 'Clipping system');
define('ZLP_MT_SLAT_TYPE_FRONT', 'Slat type front');
define('ZLP_MT_SLAT_TYPE_BACK', 'Slat type back');
define('ZLP_MT_FOIL_ARRANGEMENT', 'Foil arrangement');
define('ZLP_MT_CENTERLINE', 'Centerline');
define('ZLP_MT_SLAT_FIXATION', 'Slat fixation');

define('TEXTILE_TAPE', 'Textile tape');
define('ZLP_MT_TEXTILE_TAPE_COLOR', 'Textile tape color');
define('ZLP_MT_TEXTILE_TAPE_WIDTH', 'Textile tape width');
define('ZLP_MT_TEXTILE_TAPE_STAND', 'Textile tape stand');

define('MOUNTING_FOIL_CONFIGURATION', 'Mounting foil configuration');
define('ZLP_MT_DIST_SLAT_TO_CUT', 'Distance slat to cut');
define('ZLP_MT_DIST_FOIL_TO_CUT', 'Distance foil end to cut');
define('ZLP_MT_DIST_LEFT_RIGHT_TO_CUT', 'Distance left + right to cut');
define('ZLP_MT_DIST_SLAT_TO_PRINT', 'Distance slat to print area');
define('ZLP_MT_GLUEING_EDGE', 'Gluing edge');

define('PULL_BANDS', 'Pull Bands');
//define('ZLP_GES_F_ALIGNMENT_BAR', 'Alignment bar');
define('ZLP_MT_PULL_BANDS_WIDTH', 'Pull Bands width');
define('ZLP_MT_PULL_BANDS_THICKNESS', 'Pull Bands Thickness');
//define('ZLP_GES_F_ALIGNMENT_BAR_POS', 'Alignment bar position');
define('ZLP_MT_PULL_BANDS_TYPE', 'Alignment bar type');
//define('ZLP_GES_F_PRINT_CTRL_ELEMENTS', 'Print control elements');
define('ZLP_MT_PULL_BANDS_PRINTCTRL_02', 'Print control elements 2');
define('ZLP_MT_PULL_BANDS_PRINTCTRL_03', 'Print control elements 3');

define('EYES_RIVETS', 'Eyes and rivets');
define('ZLP_MT_EYES', 'Eyes (back)');
define('ZLP_MT_EYES_DISTANCE', 'Eyes distance');
define('ZLP_MT_EYES_SIZE', 'Eyes size');
define('ZLP_MT_EYES_DIST_FROM_FOIL', 'Eyes distance from foil');
define('ZLP_MT_EYES_FOR_ARCHIVING', 'Eyes for archiving');
define('ZLP_MT_EYES_DISTANCE_ARCHIVE', 'Eyes distance for archive');
define('ZLP_MT_EYES_SIZE_ARCHIVE', 'Eyes size for archive');
define('ZLP_MT_EYES_DIST_FOIL_ARCHIVE', 'Eyes distance from foil for archive');
define('ZLP_MT_RIVET', 'Rivet (back)');
define('ZLP_MT_RIVET_DISTANCE', 'Rivet distance');
define('ZLP_MT_RIVET_DISTANCE_WIDTH', 'Rivet distance width');
define('ZLP_MT_RIVET_DIST_FROM_FOIL', 'Rivet distance from foil');
define('ZLP_MT_DRAW_ORIGIN', 'Draw origin');
define('ZLP_MT_PACKAGING_TYPE', 'Packaging type');
define('ZLP_MT_REMARKS_02', 'Remarks');

define('ZLP_AG_F_COLOR_01', 'Color Name');
define('ZLP_AG_F_PLATE_TYPE_01', 'Plate Type');
define('ZLP_AG_F_PLATE_THICKNESS_01', 'Plate Thickness');
define('ZLP_AG_F_SPECIAL_RELIEF_01', 'Spec. Relief');
define('ZLP_AG_F_CLICHE_NUMBER_01', 'Cliche Number');
define('ZLP_AG_F_LINE_SCREEN_01', 'Line/Screen');
define('ZLP_AG_F_ANGLE_01', 'Angle');
define('ZLP_AG_F_CONT_SCR_CT_01', 'LPI Contone');
define('ZLP_AG_F_TECH_SCR_CT_01', 'LPI Linework');
define('ZLP_AG_F_DOT_TYPE_01', 'Dot Type');
define('ZLP_AG_F_DISTORTION_L_01', 'Dist. Lenghtwise');
define('ZLP_AG_F_DISTORTION_C_01', 'Dist. Crosswise');
define('ZLP_AG_F_REMARK_01', 'Remark');
define('ZLP_AG_F_PATCH_01', 'Patch');
define('ZLP_AG_F_INK_TYPE_01', 'Ink Type');

//GenSpec Flexibles
define('ZLP_AG_F_SUBSTRATE', 'Printing Substrate');
define('ZLP_AG_F_PRINT_METHOD', 'Printing Method');
define('ZLP_AG_F_CYLINDER', 'Cylinder circumference');
define('ZLP_AG_F_REPEAT_LENGTH', 'Repeat length');
define('ZLP_AG_F_EXPANSION', 'Expansion');
define('ZLP_AG_F_ANGLE_TYPE', 'Angle Type');
define('CUSTOMER_SPECIFIC_FIELDS', 'Customer Specific Fields');
define('ZLP_AG_NOTES', 'Notes');
define('ZLP_AG_BUSINESS_CATEGORY', 'Business Category');
define('ZLP_WEBCENTER_ID', 'WebCenter ID');
define('ZLP_DO_DGC', 'Plate DGC');
define('ZLP_AG_REF_JOB', 'Previous Job');

//define('COLOURS', 'Colours');
//define('ZLP_ANZF_VS', 'Number of colors for job');
//define('ZLP_AG_F_MACHINE_COLORS', 'Max. number of colors  for machine');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'Max. Ink coverage');
//define('ZLP_AG_F_COLOR_01', 'Color name');
//define('ZLP_AG_F_PLATE_TYPE_01', 'Plate type');
//define('ZLP_AG_F_PLATE_TYPE_HELP', 'Plate type helptable');
//define('ZLP_AG_F_PLATE_THICKNESS_01', 'Plate thickness');
//define('ZLP_AG_F_SPECIAL_RELIEF_01', 'spec. Relief');
//define('ZLP_AG_F_CLICHE_NUMBER_01', 'cliche number');
//define('ZLP_AG_F_LINE_SCREEN_01', 'line/screen');
//define('ZLP_AG_F_ANGLE_01', 'Angle');
//define('ZLP_AG_F_CONT_SCR_CT_01', 'con-tone screen count');
//define('ZLP_AG_F_TECH_SCR_CT_01', 'techn. screen count');
//define('ZLP_AG_F_DOT_TYPE_01', 'dot type');
//define('ZLP_AG_F_DISTORTION_L_01', 'distortion lenghtwise');
//define('ZLP_AG_F_DISTORTION_C_01', 'distortion crosswise');
//define('ZLP_AG_F_REMARK_01', 'remark');
//define('ZLP_AG_F_PATCH_01', 'patch');

//Repro Flexibles
define('TRAPPING_BLEED', 'Trapping/Bleed');
//define('ZLP_REP_F_MIN_TRAP', 'Minimum Trap');
//define('ZLP_REP_F_MAX_TRAP', 'Maximum Trap');
//define('ZLP_REP_F_STD_TRAP', 'Standard Trap');
define('ZLP_REP_F_COL_TRAP', 'Metalic color trap');
define('ZLP_REP_F_WHITE_TRAP_01', 'White trap 1');
define('ZLP_REP_F_WHITE_TRAP_02', 'White trap 2');
define('ZLP_REP_F_WHITE_PULL_01', 'White pullback 1');
define('ZLP_REP_F_WHITE_PULL_02', 'White pullback 2');
//define('ZLP_REP_F_VARNISH_TRAP', 'Varnish Trap');
define('ZLP_REP_F_VARNISH_PULL', 'Varnish pullback');

//define('ARTWORK_INFORMATION', 'Artwork information');
//define('ZLP_REP_F_MIN_LINE_POS', 'Min. line thickness pos.');
//define('ZLP_REP_F_MIN_LINE_NEG', 'Min. line thickness rev.');
//define('ZLP_REP_F_MIN_TXT_SIZE_POS', 'Min. text size pos.');
//define('ZLP_REP_F_MIN_TXT_SIZE_NEG', 'Min. text size rev.');
//define('ZLP_BB_MIN_DOT', 'min dot size on file');
//define('ZLP_REP_F_MAX_TON_VAL', 'Max. Tonal value in gradient');
//define('ZLP_REP_F_MAX_INK_COVERAGE', 'Max. Ink coverage');
define('ZLP_REP_F_ORIENTATION', 'Orientation');
//define('ZLP_CAD_F_PRINT_HEIGHT', 'Print area size height');
//define('ZLP_CAD_F_PRINT_WIDTH', 'Print area size width');
//define('ZLP_CAD_F_DISTANCE_LEFT', 'Distance left');
//define('ZLP_CAD_F_DISTANCE_RIGHT', 'Distance right');
//define('ZLP_CAD_F_DISTANCE_TOP', 'Distance top');
//define('ZLP_CAD_F_DISTANCE_BOTTOM', 'Distance bottom');
//define('ZLP_CAD_F_MIN_DIST_DIE_CUT', 'Min. Distance to Die-cut');
define('ZLP_CAD_F_PRINT_HEIGHT_TXT', 'Print area overall length');
define('ZLP_CAD_F_PRINT_WIDTH_TXT', 'Print area overall width');

define('ZLP_REP_F_CAP_TOP', 'Cap/Top');
define('ZLP_REP_F_BLEED', 'Bleed');
define('ZLP_REP_F_BOTTOM', 'Bottom');
define('ZLP_REP_F_SIDE_FRAMES', 'Side frames');
//define('ZLP_REP_C_NOTES', 'Notes/other');

//StepRepeat Flexibles
//define('ZLP_CAD_F_DIE_CUT_NO', 'CAD/Die-cut number');
define('ZLP_GES_F_MULTIUP', 'Multiup layout from customer');
define('ZLP_GES_F_1UP_RADIAL', 'Number of one-ups radial');
define('ZLP_GES_F_RAP_RADIAL', 'Rapport radial');
define('ZLP_GES_F_STAG_RADIAL', 'Staggering radial');
define('ZLP_GES_F_MOUNTED_01', 'Mounted radial');
define('ZLP_GES_F_1UP_AXIAL', 'Number of one-ups axial');
define('ZLP_GES_F_RAP_AXIAL', 'Rapport Axial');
define('ZLP_GES_F_STAG_AXIAL', 'Staggering Axial');
define('ZLP_GES_F_MOUNTED_02', 'Mounted axial');
define('ZLP_GES_F_1UP_TOTAL', 'Total amount of one-ups');
define('ZLP_GES_F_SCAT_PRINT', 'Scatter print');
define('ZLP_GES_F_OVERALL_LENGTH', 'Overall length');
define('ZLP_GES_F_OVERALL_WIDTH', 'Overall width');
//define('ZLP_GES_F_REG_MARK_TYPE', 'Registration mark type/size');
//define('ZLP_GES_F_REG_MARK_POS', 'Registration mark position');
//define('ZLP_GES_F_ALIGNMENT_BAR', 'Alignment bar');
//define('ZLP_GES_F_ALIGNMENT_BAR_POS', 'Alignment bar position');
//define('ZLP_GES_F_PRINT_CTRL_ELEMENTS', 'Print control elements');
define('ZLP_GES_F_1UP_MARK', 'One-up Mark');
//define('ZLP_CAD_F_SCANNING_MARK', 'Eye mark');
//define('ZLP_CAD_F_SIZE', 'Size');
//define('ZLP_CAD_F_COLOR', 'Color');
//define('ZLP_CAD_F_WHITE_UNDERLAY', 'White underlay');
//define('ZLP_CAD_F_KEY_MARK', 'Key mark');
//define('ZLP_CAD_F_POSITION', 'Position');
define('ZLP_GES_F_NOTES', 'Notes/other');
define('ZLP_AG_F_COLOR_1', 'Color name');
define('ZLP_GES_F_END_TO_END_1', 'End-to-end');
define('ZLP_GES_F_STAG_CUT_1', 'Staggered cut');
define('ZLP_CAD_F_TOTAL_PRINT_WIDTH', 'Total print width');
define('ZLP_GES_F_LAYOUT_POS', 'Layout position');
define('ZLP_GES_F_OFFSET', 'Offset/Stagger');
define('ZLP_GES_GAP_F_CIRCUMFERENCE', 'Plate Gap');
define('ZLP_GES_F_ALIGNMENT_BAR_SIZE', 'Alignment bar size');
define('ZLP_GES_F_ALIGNMENT_BAR_COLOR', 'Alignment bar color');

define('MOTIF_ARRANGEMENT', 'Motif arrangement');

//Gravure Cylinder
define('ZLP_GC_STAGGERING', 'Staggering 1');
define('ZLP_GC_AMT_NEW', 'Amt. new Cylinder');
define('ZLP_GC_AMT_STOCK', 'Amt. on stock');
define('ZLP_GC_AMT_OLD', 'Amt. old Cylinder');
define('ZLP_GC_OLD_CYL', 'Old Cylinder');
define('ZLP_GC_OLD_CYL_DATE', 'Old Cylinder Date');
define('ZLP_GC_MATERIAL', 'Material');
define('ZLP_GC_MATERIAL_DATE', 'Material Date');
define('ZLP_GC_NOTES', 'Notes');
define('ZLP_GC_USAGE_01', 'Usage');
define('ZLP_GC_PROCESS_01', 'Process');
define('ZLP_GC_NOMINALSIZE_01', 'Norm Size');
define('ZLP_GC_CODE_01', 'Code');
define('ZLP_GC_PROOF_01', 'Press Proof');
define('ZLP_AG_NEW_01', 'New');
define('ZLP_GC_SCREEN_DEPTH_01', 'Screen Depth');

//Offset Plate
//define('ZLP_AG_F_SUBSTRATE', 'Substrate');
define('ZLP_OF_GRAMMAGE', 'Grammage');
define('ZLP_OF_GRIPPER', 'Gripper');
define('ZLP_OF_PULL_LAY', 'Pull Lay');
define('ZLP_OF_LAY', 'Lay');
define('ZLP_OF_VIEW', 'View');
define('ZLP_OF_TURN', 'Turn around');
define('ZLP_OF_ORIGFORMAT', 'Original Format');
define('ZLP_OF_PRINTFORMAT', 'Size of printrun');
//define('ZLP_BB_COL_PROFILE', 'Job Colour Profile');
define('ZLP_OF_PLATE_SIZE', 'Plate Format');
define('ZLP_OF_VARNISH', 'Varnish Type');
define('ZLP_OF_BLANKET', 'Varnishing blanket');
define('ZLP_OF_BLANKET_OLD', 'Old varnishing blanket');
define('ZLP_OF_BLANKET_OLD_NO', 'Old varnishing blanket DB no.');
define('ZLP_OF_TOOL_NUMBER', 'Tooling Number');
define('ZLP_OF_DRAW_NUMBER', 'Drawing Number');
define('ZLP_OF_MEMO', 'Notes');

//Correction
define('CORRECTION_ON', 'Correction on');
define('CORRECTION_PER', 'Correction per');
define('CORRECTION_DATE', 'Date');
define('CORRECTION_NO', 'Correction No.');
define('CORRECTION_FROM', 'Correction from');
define('CORRECTION_COMPLETED', 'Completed');

//Approval
define('APPROVAL_VIA', 'Approved via');
define('APPROVAL_TYPE', 'Approval type');
define('APPROVAL_DATE', 'Approval Date');
define('APPROVAL_BY', 'Approved by');

//Production Art
define('ZLP_AW_BASED_ON', 'Artwork Based on');
define('ZLP_AW_ADAPTION', 'Adaptation to');
define('ZLP_AW_PURCHASE', 'Purchase Photograpy');
define('ZLP_AW_COR_LAYOUT', 'Layout Correction');
define('ZLP_AW_COR_TEXT', 'Text Correction');
define('ZLP_AW_COR_IMAGE', 'Image Correction');
define('ZLP_AW_NOTES', 'Notes');

//Creative
define('ZLP_CR_DESIGNTYPE', 'Design type');
define('ZLP_CR_PURCHASE', 'Purchasing');
define('ZLP_CR_DESIGNS', 'Amount of Designs');
define('ZLP_CR_BRIEFINGDATE', 'Briefing Date');
define('ZLP_CR_NOTES', 'Notes');

//Photography
define('ZLP_PH_CONCEPT', 'Concept');
define('ZLP_PH_PICTURES', 'Amount of Pictures');
define('ZLP_PH_BRIEFING', 'Briefing specification');
define('ZLP_PH_REFJOB', 'Job reference');
define('ZLP_PH_DIRECTOR', 'Photo director on site by');
define('ZLP_PH_ELEMENTS', 'Picture elements');
define('ZLP_PH_NOTES', 'Notes');

//Artistic Retouching
define('ZLP_AR_TEMPLATE1', 'Template 1');
define('ZLP_AR_TEMPLATE2', 'Template 2');
define('ZLP_AR_TEMPLATE3', 'Template 3');
define('ZLP_AR_REFJOB', 'Reference Job');
define('ZLP_AR_MASK', 'Knock out mask');
define('ZLP_AR_NOTES', 'Notes');

//Mockup
define('ZLP_MC_AMOUNT', 'Amount');
define('ZLP_MC_PURCHASING', 'Purchasing');
define('ZLP_MC_NOTES', 'Notes');

//Technical Services
define('PROOF_PROFILE', 'Proof Profile');
define('ZLP_TS_PROFILE', 'Profile based on');
define('ZLP_TS_PROFILE_NAME', 'New Profile name');
define('ZLP_TS_PROFILE_ID', 'New Profile Spec ID');
define('ZLP_TS_PROOF', 'Proof Type');
define('ZLP_TS_REFERENCE', 'Reference');
define('ZLP_TS_CMYK', 'Profile (CMYK)');
define('ZLP_TS_SPOT_COLORS', 'Spot Colors');
define('ZLP_TS_DGC', 'DGC');
define('ZLP_TS_NOTES_01', 'Notes');

define('PRINTING_APPROVAL', 'Printing Approval');
define('ZLP_TS_INSTIGATION', 'Instigation');
define('ZLP_TS_CONTACT_PRINTER', 'Contact person of printer');
define('ZLP_TS_CONTACT_CUSTOMER', 'Contact person of customer');
define('ZLP_TS_REF_ARTWORK', 'Reference artwork');
define('ZLP_TS_REF_SPOT_COLORS', 'Reference spot colors');
define('ZLP_TS_PRINT_ANALYSIS', 'Print analysis');
define('ZLP_TS_NOTES_02', 'Notes');

//CDI Worksheet
define('CDI_WORKSHEET', 'CDI Worksheet');
define('CDI_WORKSHEET_FOR_ORDER', 'Worksheet CDI for Order');
define('DESCRIPTION', 'Description');
define('FINISH_DATE', 'Finish Date');
define('PERSONAL_NAME', 'Esko Operator');
define('FACE_PRINT', 'Face Printing');
define('PRODUCTION_NO', 'Production no.');
define('BATCH_NO', 'Batch no');
define('BOX_NO', 'Box no');
define('TYPE', 'Type');
define('CDI_IMAGING_OPERATOR', 'CDI Imaging operator');
define('EXPOSURE_OPERATOR', 'Exposure operator');
define('QUALITY_CONTROL', 'Quality control');
define('DATE', 'Date');

define('CDI_ZLP_MT_RELIEF_DEPTH', 'Relief');
define('CDI_ZLP_FP_NO_OF_PLATES_01', 'Amount');
define('CDI_ZLP_AG_F_PLATE_TYPE_01', 'Type');
define('CDI_ZLP_FP_DIGITAL_ANALOGUE_01', 'D / A');
define('CDI_ZLP_AG_F_PLATE_THICKNESS_01', 'Thickness');
define('CDI_ZLP_GES_F_STAG_CUT_01', 'Staggered');