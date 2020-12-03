# wp_gfdp_custom_folders
GFDropBox Custom Upload folders

We need to make a code modicifation for Graivity Frorms Dropbox addon. We use gravity forms Dropbox addon to let users to upload files and pictures, and this addon stores files in our dorpbox account. But all the files from all the users are saved in one folder. So we need to make when we are cleaning a new users we create his folder, and all the pictures uploaded by this user would be stored in his folder. Ideal would to make so there would be sub folders for the forms we use to to the file upload. And we also need to make an image resize so pictures are uploaded in lower quality.

Every user we create in gravity form get user role: Manager, and it has its own id (Owner PID) which is generated automatically, and re unique, 
We have group of users they are coming to the page without a login, User role worker, but in url they bring their own PID (ID): https://worker.nu/worker/?PID=15b45a So by this PID: 15b45a we have all the info: 
who is the owner of this worker, So if image is uploaded by the user with his ID, we know who is the owner of the this user and photos has to go to the Owners folder and sub folders. will explain the hirarcy later. 
We have like 5-7 different gravity forms (Issue report, exyra hours report, fixed job report, orders,) in our site, when each from is submited we have all the fields in the entry: 
Worker pid worker owner PID also when user are uploading the pictures normaly he slects in the form for which project he uplaod the pictures. so the tree of folder has to look like:


- Company name 
-- Project name {project start day} (users selects it) 
--- Form name where the project was submitted 
---- User (who uploaded) / date of upload. 
------ there the pictures has to land.


