RENAME TABLE `adminBookmarks` TO `SystemAdminBookmarks`;
RENAME TABLE `cards` TO `BabeskCards`;
RENAME TABLE `class` TO `KuwasysClasses`;
RENAME TABLE `classTeacher` TO `KuwasysClassteachers`;
RENAME TABLE `global_settings` TO `SystemGlobalSettings`;
RENAME TABLE `Grades` TO `SystemGrades`;
RENAME TABLE `GroupModuleRights` TO `SystemGroupModuleRights`;
RENAME TABLE `Groups` TO `SystemGroups`;
RENAME TABLE `jointUsersInClass` TO `KuwasysUsersInClasses`;
RENAME TABLE `kuwasysClassUnit` TO `KuwasysClassCategories`;
RENAME TABLE `LogCategories` TO `SystemLogCategories`;
RENAME TABLE `Logs` TO `SystemLogs`;
RENAME TABLE `LogSeverities` TO `SystemLogSeverities`;
RENAME TABLE `meals` TO `BabeskMeals`;
RENAME TABLE `Message` TO `MessageMessages`;
RENAME TABLE `Modules` TO `SystemModules`;
RENAME TABLE `price_classes` TO `BabeskPriceClasses`;
RENAME TABLE `schbas_fee` TO `SchbasFee`;
RENAME TABLE `schbas_inventory` TO `SchbasInventory`;
RENAME TABLE `schbas_lending` TO `SchbasLending`;
RENAME TABLE `schbas_selfpayer` TO `SchbasSelfpayer`;
RENAME TABLE `Schooltype` TO `SystemSchooltypes`;
RENAME TABLE `schoolYear` TO `SystemSchoolyears`;
RENAME TABLE `soli_coupons` TO `BabeskSoliCoupons`;
RENAME TABLE `soli_orders` TO `BabeskSoliOrders`;
RENAME TABLE `TemporaryFiles` TO `SystemTemporaryFiles`;
RENAME TABLE `UsercreditsRecharges` TO `BabeskUsercreditsRecharges`;
RENAME TABLE `UserInGroups` TO `SystemUsersInGroups`;
RENAME TABLE `users` TO `SystemUsers`;
RENAME TABLE `usersInClassStatus` TO `KuwasysUsersInClassStatuses`;
RENAME TABLE `usersInGradesAndSchoolyears`
	TO `SystemUsersInGradesAndSchoolyears`;
RENAME TABLE `jointClassTeacherInClass` TO `KuwasysClassteachersInClasses`;
RENAME TABLE `orders` TO `BabeskOrders`;
RENAME TABLE `priceGroups` TO `BabeskPriceGroups`;