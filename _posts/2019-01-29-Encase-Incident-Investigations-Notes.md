---
layout: post
author: Donald Murchison
category: n0tes
title: "Encase - Incident Investigation"
---

Here are my personal notes from OpenText "IR250 - Incident Investigation" course (Nothing was copied out of the Encase copyrighted manual). I took almost all of the Encase courses and this was by far my favorite. The instructors provide excellent resources and go way beyond just teaching how to use Encase. While my notes are very shorthand, the course went in-depth on many non-Encase topics, like NTFS. If you like getting in to the technical "weeds" and parsing hex this course is for you.

## ***Glossary***

[Incident Response Considerations](#incident-response-considerations)
- Legal Considerations
- Education
- Policies & Procedures
- Equipment
- Flexibility & Mindset

[Examination Methodologies and Options](#examination-methodologies-and-options)
- 4 Basic Security Principles
- Options
- Starting Investigation - Recommendation (3 steps)

[Encase Endpoint Investigator](#encase-endpoint-investigator)
- Network Preview
- Sweep Enterprise

[Windows Registry and Autoruns](#windows-registry-and-autoruns)
- 5 Main Registry Hives
- Time Zone Information
- Autoruns

[NTFS Metadata Files & $MFT Overview](#ntfs-metadata-files--mft-overview)
- Volume Boot Record
- Metadata Overview

[$MFT Record Header Details](#mft-record-header-details)
- Record Header
- $MFT Attributes

[Standard Information and Filename Attribute Details](#standard-information-and-filename-attribute-details)
- Standard Information Attribute
- Filename Attribute

[Data Attribute Details](#data-attribute-details)
- Data Attribute
- Non-Resident Data
- Resident Data
- Alternate Data Streams (ADS)

[USN Change Journal Details](#usn-change-journal-details)
- Implementation
- Record Content
- Procedure

[$LogFile Details](#logfile-details)
- Implementation
- Procedure

[EFS and Bitlocker VHDs](#efs-and-bitlocker-vhds)
- NTFS Encrypting File System (EFS)
- VHDs Encrypted with Bitlocker

[Windows Event Logs](#windows-event-logs)
- Old Version (EVT)
- Current Version (EVTX)
- Procedure

[Prefetch](#prefetch)
- PFDump (Enscript)
- Bookmark Filter Plugin

[Link Files and Jumplists](#link-files-and-jumplists)
- Jumplists
- Importance of Link Files and Jumplists
- Important Components of Link Files
- Distributed Link Tracking Service
- Recent User File System Activity
- Jumplist Locations
- Encase Processor
- Enscript
- Using (Own) Object ID to find Target File
- Using Conditions to find Files with (Own) Object ID
- $R\<6 chars\>.\<original extension\> and $I\<6 chars\>.\<original extension\>
- Matching Username to SID
- Parsing $I files
- Missing $I file

[ShellBags](#shellbags)
- USRClass.dat
- Encase Processor
- Enscript

[Volume Shadow Service](#volume-shadow-service)
- Volume Shadow Copy
- Recovery Methods

[Memory Analysis](#memory-analysis)
- RAM Usage
- Volatility
- Common Analysis

[Browser Artifacts](#browser-artifacts)
- Configuration
- Browsing History
- Bookmarks
- Cache Content
- Cookies
- Web Server Data
- Local Storage
- Form Data and Web Passwords

[Internet Explorer and Edge](#internet-explorer-and-edge)
- Bookmarks
- Cache
- History
- WebCacheV01.dat
- Getting Accurate Access Count
- Recovering Deleted History from IE
- Microsoft Edge (Barely went over)

[Mozilla FireFox](#mozilla-firefox)
- FireFox User Profiles
- Base Artifact Location
- Pref.js
- Encase Processor
- Consequences of SQLite Write -Ahead-Logging (WAL)
- Handling WAL
- Secure Wipe

[Google Chrome](#google-chrome)
- Chrome User Profiles
- Base Artifact Location
- Preferences
- Encase Processor
      

### ***Incident Response Considerations***
**Legal Considerations**
- BYOD
- EU Privacy Laws
- What is in Scope?

**Education**
- Education and Training especially important for Incident Investigation
- Knowledge of new and popular vulns and exploits

**Policies & Procedures**
- Corporate Policies
    - Are the drives encrypted?
    - Removable devices allowed?
    - What procedures regarding data?
- Software Policies
    - Which software is installed? Should be installed?
- Investigative Procedures
    - Will corporate procedures stand up to legal scrutiny?

**Equipment**
- What to bring?
- Camera, storage media, anti-static bag, etc...

**Flexibility & Mindset**
- Need to remain flexible based on situation
- IR will be very dependent on what you find

### ***Examination Methodologies and Options***
**4 Basic Security Principles**
- No action should change data held on media (unless absolutely necessary)
- If changed, person must be competent and able to give explanation of relevance and
implications of actions
- Audit Trail must record all processes applied
- Person in charge has overall responsibility

**Options**
- DeadBox
    - Refers to "pulling the plug"
    - No RAM or volatile data
    - Encryption might make this not examinable
- Live Booting
    - Turned off and booted from USB
    - RAM still lost
    - Can acquire hardware RAID arrays as a logical unit
    - May not be possible if can't change bios
- Local Acquisition
    - Data acquired locally while machine is running
    - Data acquired locally while machine is running
    - RAM, screenshots, and network info can all be captured
    - Encrypted Drives can be captured
- Network Acquisition
    - Data acquired remotely while machine is running
    - Similar benefits to above
    - Local login not required, attacker may not be as aware of actions

**Starting Investigation - Recommendation (3 steps)**
1. Account for Time Zone information (Registry)
2. Map volumes to correct drive letters (Registry)
3. Recover any hidden or deleted volumes

### ***Encase Endpoint Investigator***

**Network Preview**<br>
*Notes*
- Does not pull data across until necessary
    - When the evidence is opened, only pulls MBR, VBRs, and MFTs across wire
    - File contents are not pulled across until the file is opened or processed
- Cannot index acquired data ( All other processing is possible)
- Preview does not show changes which occur while viewing
    - Can rescan to show these changes
- If volume is encrypted, need to preview logical volume not physical device

*Settings*
- Make sure to use the "File in Use" setting when acquiring
    - This will exclude the files from verification in case they are being written to on remote machine

**Sweep Enterprise**<br>
*Notes*
- Similar to Processor on network preview but can be done across large number of machines
- Safe -> Sweep Enterprise -> Create Scan ( Import Targets -> does not support wildcard or network range)
- Results will be in Analysis Browser (stored in SQL database)
- To view file structure, in Sweep Evidence file, right click "Artifacts" (will be root folder) -> "Artifacts" -> "view Entries"
- Can bookmark selected, save selected to reports, and select multiple reports for quick and easy
report of incident
- If large amount of RAM on machine, this is useful to collect some volatile data first, before trying to acquire all of RAM

*Settings*
- 3 options
    - System Info Parser
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Same as options in Processor for normal Network Preview
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Select "Live Registry Only" option
    - Snapshot
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Open Ports, Files, Processes
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Network Information (ARP, DNS, IP config)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Time Zone information
    - File Processor
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Use this to run file collection on machine
<br>&nbsp;&nbsp;&nbsp;&nbsp;- This will take much longer, and retrieves file contents (Might want to do this as secondary)
- This can affect current state of computer, need to be aware of what it is doing. Need to get this question answered somehow

### ***Windows Registry and Autoruns***

**5 Main Registry Hives**
1. NtUser.dat (User Specific - C:\Users\\\<username\>\ )
- Protected Storage for User, MRU lists, User's preferences
2. SOFTWARE ( C:\Windows\System32\config\ )
- All installed programs on the system and their settings
3. SYSTEM
- System settings (Time Zone, Mounted Devices, Services...)
4. SECURITY
- Security Settings (Can have cached Passwords)
5. SAM
- Security Settings and User Account Management( Usernames , Group Names, SID, Password Hint...)

**Time Zone Information**<br><br>
*Manual*
- HKLM\System\ControlSet00X\Control\Time Zone Information
- To find Current Control Set Number
    - HKLM\System\Select\Current

*Enscripts*
- Quick Registry Browser
    - Mounts Registry Hive in RAM
    - Nice easy interface and won't affect processing later
    - If open it normally, processor will go through it (Increase process time)
- Time Zone Info Prior to Processing
    - This is basically same as link in Full Pathways
    - One difference is it will place results in Bookmarks
    - Full Pathways will display in pop up

**Autoruns**

*Manual*
- Startup Folder (AppData\Roaming\Microsoft\Windows\StartMenu\Startup)
- Tons of registry locations where these can be stored  
    - Services (System\ControlSet00X\Services\\<Service\>\Start)
    - Software(Software\Microsoft\Windows\CurrentVersion\(Run|RunOnce))
<br>&nbsp;&nbsp;&nbsp;&nbsp;- I think any "Run" or "RunOnce" key

*Encase Processor*
- System Info Processer (Options to Select)
    - Autoruns
    - Advanced -> Autostart Folder (Blue Check)

*Enscripts*
- Windows Autostart Programs Result

### ***NTFS Metadata Files & $MFT Overview***

**Volume Boot Record**<br>
![(Missing) NTFS Boot Sector Diagram]({{ site.url }}/assets/notes/ii/ntfs_boot.png)

*Important Pieces*
- 3 byte jump code and Original Equipment Manufacturer ID (OEM)
- Volume Serial Number
- Size of Clusters
- Location of $MFT
- Location of $MFTMirr

**Metadata Overview**
- $MFT
    - Similar to relation DB
    - Records are 1024 bytes
    - Has Records for all files and folders on volume
    - Can store file in record as resident data if small enough
    - Roughly 12.5% of Volume Size
    - File identifier = 0
- $MFTMirr
    - Mirror of first four MFT records
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $MFT
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $MFTMirr
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $LogFile
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $Volume
    - File identifier = 1
- $LogFile
    - Size of LogFile depends on size of disk
    - Journaling and Tracking takes place here
    - File identifier = 2
- $Volume
    - Contains information about volume, volume label, version, name...
    - File identifier = 3
- $AttrDef
    - Defines attributes in $MFT (names, numbers descriptions)
    - Standard Information Attribute, Data Attribute, Filename Attribute...
    - File identifier = 4
- "."
    - Root file name index
    - In Encase, this is represented by the volume itself
    - In Transactional NTFS systems, has alternate data stream $TXF_DATA
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Tracks transactional data
    - File identifier = 5
- $Bitmap
    - Shows which clusters are available (allocated - 1 or unallocated - 0)
    - Each byte represents 8 clusters
    - File identifier = 6
- $Boot
    - Beginning of VBR
    - Includes BPB (Bios Parameter Block) used to mount volume
    - Also bootstrap loader code if bootable
    - Serial Number
    - File identifier = 7
- $BadClus
    - Contains bad clusters for the volume
    - File identifier = 8
- $Secure
    - Used to implement file system security (permissions and ownership) on NTFS volumes
    - Has four streams
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $SDS
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $SII
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $SDH
<br>&nbsp;&nbsp;&nbsp;&nbsp;- \<blank\>
    - File identifier = 9
- $Upcase
    - Converts lowercase characters to matching Unicode uppercase
    - File identifier = 10
- $Extend
    - Is a folder containing files used for extended NTFS functionality
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Reparse points
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Disk quotas
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Change log journaling ($UsnJrnl)
    - File identifier = 11

### ***$MFT Record Header Details***
![(Missing) File Record Segment Header Diagram]({{ site.url }}/assets/notes/ii/record_header.png)

**Record Header**
- FILE0 - 56 bytes (After XP)
- FILE* - 48 Bytes (XP and earlier)
- Ignore bytes length for "Reserved for update sequence array?", for FILE0 "Common location of 1st attribute will be at 56 bytes, and reserved area (including default location for update seq array portion) will most likely be 10 bytes, not 24 as shown

*Update Sequence Array*
- First two bytes after "FILE" refer to offset of array (0x30 or 0x2a)
- This is an error checking mechanism
- Update seq array refers to 3 two byte sections
    - The first two byte section is randomly generated
<br>&nbsp;&nbsp;&nbsp;&nbsp;- This value replaces the last two bytes of each sector (Sector 512 bytes -Record 1024 bytes)
    - The next two sections are the values of whatever the first two bytes replaced at the end of the sectors (These are replaced to original values in RAM)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- **Important to replace these values when parsing a record manually
- If the end of the two sectors don’t match first two bytes while be viewed as corrupted 

*Sequence Number*
- When a file or folder is deleted, MFT record sequence number is incremented, helps associate version in track USN change journal because MFT records can be reused multiple times

*Hard Link Count*
- Refers to the number of times a file is linked from a directory
- I believe the number of filename attributes will match this

*Flags*
- If flags has bit 1 set, the file is in use.
- If flags is set to 0, then the file has been deleted

*Enscripts*
- NTFS Single MFT Record and Attributes
    - Bookmark and decode header from the highlighted file's MFT
    - Will also bookmark each of the record's MFT attributes
- Bookmark MFT Record for Highlighted file
    - Bookmark the MFT record for the entry currently highlighted
- NTFS StepMOM
    - Will take a single MFT Record and parse out ALL of the attributes listed below
    - Or search all blue checked objects for MFT records and attempt to parse out their attributes
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Standard Information
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Attribute list
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Filename
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Object ID
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Volume Name
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Volume Info
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Data
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Index Root
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Index Allocation

*Plugin*
- Similar results to the first two enscripts, makes easier to access by adding to right click menu 

**$MFT Attributes**<br>
![(Missing) Attribute Type List]({{ site.url }}/assets/notes/ii/att_type.png)

*Resident Attribute Header*

![(Missing) Resident Attribute Header]({{ site.url }}/assets/notes/ii/res_header.png)

*Non-Resident Attribute Header*

![(Missing) Non-Resident Attribute Header]({{ site.url }}/assets/notes/ii/nonres_header.png)

### ***Standard Information and Filename Attribute Details***

**Standard Information Attribute**
- De Facto location for storage of file system timestamps and DOS attributes
- Attribute type: 16 - (0x10 00 00 00)
- 96 bytes (0x60 00 00 00)
- Diagram below not showing 24 bytes of Resident Attribute Header 

![(Missing) Standard Information Attribute]({{ site.url }}/assets/notes/ii/sia_diagram.png)

*Timestamps*
- Maintains four basic timestamps
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Created
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Last written
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $MFT record last modified
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Last accessed
- Stored in 8 byte format called "FILETIME"
- Represents number of 100-nanosecond intervals since January 1st, 1601
- "FILESTAMP" times in $SIA are in UTC/GMT.
- Occupy a total of 32 bytes
- Later versions of Windows do not update the "Last Accessed" time by default.

*Enscript*
- NTFS Standard Information Attribute Reader
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Will parse and bookmark the $SIA in to above components
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Dates will be in Decoded Stream Data
- MFT Date Comparator
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Set "Created date difference (in minutes)"
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Compares the $SIA dates to the dates in the $FNA (Filename Attribute below)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Anti-Forensic tools often only change the $SIA timestamps, so a difference in the two could indicate timestamps have been tampered with

**Filename Attribute**
- Filename attribute stores the following:
    - Filename
    - Filename type
    - Parent Directory Reference
<br>&nbsp;&nbsp;&nbsp;&nbsp;- MFT record number (6 Bytes) and sequence number (2 Bytes)
    - Timestamps (Same 4 as above)
- Attribute Type: 48 - (0x30 00 00 00)
- Variable Length ~120 bytes
- Typically at least 2 $FNA, a short name and long name
- Diagram below not showing 24 bytes of Resident Attribute Header

![(Missing) Filename Attribute]({{ site.url }}/assets/notes/ii/fn_diagram.png)

*Enscript*
- NTFS File Name Attribute
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Will parse and bookmark the filename attribute in to above parts

*Extra*

![(Missing) Flags for SIA and FNA]({{ site.url }}/assets/notes/ii/sia_fna_flags.png)

![(Missing) Name Types]({{ site.url }}/assets/notes/ii/name_types.png)

### ***Data Attribute Details***

**Data Attribute**
- If a file has any data it will have a Data Attribute
- Each attribute references a stream of data
- Primary Streams are unnamed - ADS will have a name
- Both can be resident or non-resident

**Non-Resident Data**

![(Missing) $Data and data runlists]({{ site.url }}/assets/notes/ii/data_diagram.png)

- Data run list will follow the above format and end with 0x00 when no more fragments
- The Run offset uses signed integer

**Resident Data**

- This uses the same Resident Attribute Header has before (24 bytes)

![(Missing) Resident Attribute Header]({{ site.url }}/assets/notes/ii/res_header.png)

- The data follow directly after the header.
- 3 good ways to determine if file is resident
    - Both the logical and physical size will be equal
    - There will no file slack
    - File will not typically begin at the 0 offset of sector or cluster

*Enscripts*
- NTFS Data Attribute Reader
    - Parse and bookmark the data attribute and the data runs

**Alternate Data Streams (ADS)**
- Used for Zone Identifiers (3 can indicate downloaded from internet)
- Used for transactional NTFS, by some internal NTFS files, document metadata...
- Can store encryption keys used by NTFS Encrypting File System (EFS)

*Building Encase condition to find ADS*
- New Condition
    - If isStream has value AND if NOT (Name find \<Default Windows Streams\>)
- This will find a lot of user defined streams

### ***USN Change Journal Details***

- Update Sequence Number Change Journal
    - This is the same update sequence number in $SIA
- When any change is made to file or directory, Journal is updated with description of change and file affected
- This can be helpful in proving a user knew about the file, and discovering original file names to search for other evidence like link files

**Implementation**
- Located in the $Extend folder
- Has 2 alternate data streams
    - $J
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Contains the actual change-journal records
    - $Max
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Contains information about the current state/configuration of journal

**Record Content**
- Version
- Target $MFT file reference
- Parent $MFT file reference
- USN
- Timestamp
- 1 or more reasons stored as bit field value (Table on page 168 in book)
- 1 or more source information flags stored as bit field value
- Target Object's security identifier
- Target file attributes
- Target Filename

**Procedure**
- Find the File ID and Sequence Number for file of interest
    - Changes to names to not change the sequence number
- Select $USNJrnl-$J
- Run Enscript - NTFS $UsnJrnl Parser
    - Current View - Selected
    - Set Condition
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Make a folder for each file of interest
<br>&nbsp;&nbsp;&nbsp;&nbsp;- If TargetMFTNum = XXXXX AND If TargetMFTSeqNum = XXXXX
    - This will bookmark and export the results (Much easier to read CSV in excel)
- We can now see reasons for entries, but more importantly a history of the file and folder names

### ***$LogFile Details***

- Revolving transaction log for $MFT (Allows for recoverability)
- As metadata changes, both original data and changes written to $LogFile
    - This is before any changes actually written to $MFT
    - Once the $LogFile is full the system will force all writes to $MFT
    -$LogFile is continuously overwritten

**Implementation**
- Default Size - 65536 bytes
- Separated into 4096 byte pages
- Two Main Types of Areas
    - Restart
<br>&nbsp;&nbsp;&nbsp;&nbsp;- RSTR
<br>&nbsp;&nbsp;&nbsp;&nbsp;- 2 of these - 1 is backup
    - Record
<br>&nbsp;&nbsp;&nbsp;&nbsp;- RCRD
<br>&nbsp;&nbsp;&nbsp;&nbsp;- First 2 records will be "buffer page areas" (1 is backup)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Records written to "buffer page area", then moved to "normal page area" when full

**Procedure**
- Encase Processor
    - Can use the Windows Artifact Parser option
    - This falls under the $MFT transfers category
    - We should be able to see file name which would tell us if worth to do the next step
- NTFS Log Tracker (3rd Party Tool)
    - Export $MFT and $LogFile out of Encase
    - Open NTFS Log Tracker
    - Set LogFile
    - Set MFT
    - Click Parsing
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Set SQLite DB Filename and File Path
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Click button on left (It's in Chinese)
    - This will give you two options
<br>&nbsp;&nbsp;&nbsp;&nbsp;- CSV file
<br>&nbsp;&nbsp;&nbsp;&nbsp;- SQL DB
    - Easier to use SQL DB (SQLite Expert Personal - Free Tool)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Select * from LogFile where FullPath Like '%\<filename\>%'
    - This will give us the filename info like Processor, but with a lot more information such as reason for entry
- The File Paths, dates and times, names of link files, can be used to investigate further
- The Short Volume Serial Number will be in the link files

### ***EFS and Bitlocker VHDs***

**NTFS Encrypting File System (EFS)**
- EFS makes use of $EFS ADS to store one or more decryption keys
    - Any of these can be used to decrypt the data in a file's primary data stream
- The decryption key is encrypted in the EFS and requires Windows credentials to decrypt
    - The EFS can have multiple key to user associations, this mean multiple accounts can decrypt
the file
    - The user accounts which can decrypt the file can be seen in plain text in the EFS

*Decrypting the files*
- Data Recovery agents are used as a failsafe so as to allow the recovery of a user's EFS-
encrypted data if they forgot password
- Obtain data recovery key (.pfx file)
- Obtain password for recovery key
- View -> Secure Storage -> <Hamburger> -> Enter Items -> Fill in password and key location
    - This should allow Encase to automatically decrypt files

**VHDs Encrypted with Bitlocker**
- Encase cannot mount Virtual Hard Disks as compound files so need to make it available externally
- 3 ways to do this
    - Extract a copy of the VHD file
    - Make the file available using VFS (Use with Parent Directory)
    - Make the file available using PDE (Use with Parent Directory)
- Once the file is available externally drag it in to Encase
    - Now will follow the same procedure as decrypting any Bitlocker drive

*BitLocker Recovery Key*
- Need the BitLocker recovery key to decrypt
- They show 3 ways
    - Try to find a text file with information
    - Collect from AD
    - Use Passware to dump the key from a copy of RAM

### ***Windows Event Logs***

- Windows event logs can be very important to an investigation

![(Missing) Windows EventCodes]({{ site.url }}/assets/notes/ii/win_eventcodes.jpg)

**Old Version (EVT)**
- Application
    - \System32\config\AppEvent.Evt
- System
    - \System32\config\SysEvent.Evt
- Security
    - \System32\config\SecEvent.Evt

**Current Version (EVTX)**
- \System32\winevt\Logs\*.evtx

**Procedure**
- Enscript - Windows Event Log Export
    - Can export the event logs and view in windows (Didn’t like Encases view, this seems better)
- Encase Processor
    - Windows Event Log Parser
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Set an entry condition ( Can do "ext equal to evtx")
    - Case Analyzer
<br>&nbsp;&nbsp;&nbsp;&nbsp;- After processing view results in case analyzer (I do not believe this pulls in all Event
Logs)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Can filter on constraints (e.g. Logon Type and EventID)
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Need whitespace included in logs (tabs)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Select Events and bookmark them (Report is much easier to read)

### ***Prefetch***

- \<Volume Letter\>:\Windows\Prefetch
- *.PF files
- Windows tracks apps for roughly ten seconds when started and stores data in prefetch files
- This allows us to find files and folders accessed by app, dlls loaded, lots of valuable information
- Prefetch will be executable name followed by 4 byte hash value based on path or command line

**PFDump (Enscript)**
- They selected all prefetch files and ran PFDump
- This allowed us to find programs we did not know about that accessed our malicious program
- Results will be in the Bookmark Folder (Folder for each PF file)
    - 5 bookmarks in this folder
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Prefetch file itself
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Hash Verification Result
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Prefetch Core Data
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Run count
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Last 8 timestamps
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Device Run Path
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Prefetch Folder Data
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Folders that were accessed by the executable
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Prefetch File Data
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Files that were accessed by the executable

**Bookmark Filter Plugin**
- This allows you to filter bookmarks by writing "conditions" (Helpful if parsed all prefetch files)
    - For Prefetch
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Comment find <files of interest>
- This can be incredibly useful for more than just prefetch, any bookmarks can be filtered

### ***Link Files and Jumplists***

**Jumplists**
- Collection of link files that appear when an applications button is right clicked in the task bar
- Recent or Frequent Sites can appear in jumplists as well

**Importance of Link Files and Jumplists**
- Can show a user accessed file, folder, or application
    - Might show that a volume was once attached (like USB, E:)
- A purposefully created link file or pinned jumplist may infer guilty knowledge by showing a user was aware of it and intended to access more than once
- May show that a particular application was installed

**Important Components of Link Files**
- Link files have tons of information even MAC addresses listed below are a couple
- Timestamps
    - Created - can refer to first time the target object accessed
    - Last Written - can refer to the last time target object accessed
    - Target Object Dates - link files also store the created, last written, and last accessed timestamps
- Base Name
    - Can be sorted on and search for drive letters
- Volume and Birth Volume ID
    - The link file will have the Volume ID of the target files current location and the Volume ID of the target file when the link was created
    - This could show if opened on USB then moved to disk and opened
- Object (own)and Birth Object ID
    - Special attribute in target files $MFT used for tracking the file
    - Can be used to prove file/folder is target of link, even if it has been renamed or moved
    - Can be used to show file/folder has been target of link or jumplist even if link has been deleted
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Can show that it was accessed at somepoint

**Distributed Link Tracking Service**
- This allows a shortcut link file to work even if the target file is moved or renamed
- This works provided target is located on NTFS volume and moved to
    - Another folder on the same volume
    - A folder on another NTFS volume on same machine
    - A folder on NTFS volume, belonging to another machine in the same Windows Domain (network share)
- When link file created, Object ID is added to a special attribute in the target file $MFT record

**Recent User File System Activity**
- AppData\Roaming\Microsoft\Windows\Recent
- Link files here have been created automatically for files that have been recently used

**Jumplist Locations**
- Sub Folders in the Recent Folder
    - Automatic Destinations
<br>&nbsp;&nbsp;&nbsp;&nbsp;- \<App ID\>.automaticDestinations-ms
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Contains one or more link-file-streams
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Acts as MRU list
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Maintained by OS on behalf of apps with custom jumplist
    - Custom Destinations
<br>&nbsp;&nbsp;&nbsp;&nbsp;- \<App ID\>. customDestinations-ms
<br>&nbsp;&nbsp;&nbsp;&nbsp;- For apps that offer custom jumplists
<br>&nbsp;&nbsp;&nbsp;&nbsp;- e.g. Google Chrome, the websites or files that pop up on home page or blank tab

**Encase Processor**
- Windows Artifacts -> Link Files
- Results will be in the Artifacts page

**Enscript**
- Link File and Jump List Parser
- Will Export a lot of information in to a tsv file
- Will have all of the Volume and Object IDs including sequence number
    - Could maybe use this for the USNJrnl parser

**Using (Own) Object ID to find Target File**
- Attribute Helper Plugin
    - Select Files and "View/Bookmark attributes from selected files"
    - Sort by attribute name, find "Own Id" with matching entry

**Using Conditions to find Files with (Own) Object ID**
- Condition -> New
- Filters Tab -> AttibuteValueRoot
    - Full Path equal to "Object Identifier" AND isFolder equal to true
- If \<Filter Above\> has a value AND File Ext not equal to "lnk"

Recycle Bin
- Recycle bin can track when files/folders were deleted and their original location
- Can also recover files in the Recycle Bin
- \<Volume Letter\>:\$Recycle.Bin
- Consists of subfolders of User SIDs corresponding to the user who deleted the file

**$R\<6 chars\>.\<original extension\> and $I\<6 chars\>.\<original extension\>**
- When a file goes to the recycle bin, a $I(index) and $R(original) file are created with a matching 6
chars
- $I Index file contains
    - Original Path
    - Time of Deletion (Time moved to recycle bin)
    - Logical Size
- $R file will be the original file data

**Matching Username to SID**
- Encase will automatically check the SAM registry hive for SID and user information when Evidence is loaded and should translate automatically
- Domain user accounts will not be stored in the SAM, could get Username\SID mapping from Domain Controller

*Manually*
- Can also use user profiles to try to determine SID-username
    - Examine SIDs associated with permissions of User Profile Folders in Encase
    - Look in registry to determine ownership of user-profile folder
<br>&nbsp;&nbsp;&nbsp;&nbsp;- HKLM\Software\Microsoft\WindowsNT\CurrentVersion\ProfileList
- Once SID\Username is determined, add to User List
    - Secure Storage tab -> "Hamburger" -> User List -> New

*Automatically*
- Right Click \<Disk\> in Tree Pane -> Device -> Analyze EFS

**Parsing $I files**
- If a file is emptied or restored from the Recycle Bin the $I file will be deleted
- This can be recovered by Encase, but might will not parse it if orphaned
- Enscript - Parse $I Recycle Bin Files can be used for this

**Missing $I file**
- A file might be emptied from Recycle bin and recovered by Encase
- If the $I file is missing, we can try to search the UsnJrnl for $MFT record number and $MFT Seq Num minus 1 to find path and file name

### ***ShellBags***
- Set of registry keys to maintain size, view, icon, and position of a folder when using Explorer
- Can persist for Directories even after it has been removed
- Help with the presence and tracking of folder
- Does not always have to be physical folders (Libraries and Control Panel)
- Can help find folders which names have been changed and prove the user knew about these files
- The root of the Shellbag will always refer to Desktop
- Two registry locations
    - HKCU\Software\Classes\Local Settings\Software\Microsoft\Windows\Shell
    - HKCU\Software\Microsoft\Windows\Shell

**USRClass.dat**
- \<Volume Letter\>:\Users\\<username\>\AppData\Local\Microsoft\Windows\
- View in quick registry browser, and navigate to shell registry key
    - Bag Sub Key Components
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Name (index number aka "node slot"
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Shell and ComDlg (Doesn't always have both)
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Shell refers to settings when viewed in Windows Explorer
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ComDlg refers to settings when viewed via common dialog box (opening and saving files)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- These folders may have some settings here or another sub folder named as a GUID
<br>&nbsp;&nbsp;&nbsp;&nbsp;- GUID subfolder tends to have much more useful information
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- LogicalViewMode
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- IconSize, etc.
    - BagMRU Sub Key
<br>&nbsp;&nbsp;&nbsp;&nbsp;- This will tell us which folder each shell bag entry relates to
<br>&nbsp;&nbsp;&nbsp;&nbsp;- BagMRU represents Desktop and all folders beneath are children
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- "This PC" counts as a child and how all folders can be included as children of
Desktop
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Also has value "MRUListEx" which specifies the order in which child folders were last accessed

**Encase Processor**
- Windows Artifacts -> ShellBags
- Automatically Parse the Shell Bag Entries
- Results will be in Artifacts
- Can bookmark and then use bookmark filter plugin to search for TargetMFTNum and TargetMFT SeqNum

**Enscript**
- ShellBags Parser
- Select User Folder you are interested in
- This will export to CSV and Bookmark results
- Allows ability to only look at specific users shell bags
- Since bookmarks everything, can use bookmark filter plugin to search for TargetMFTNum and TargetMFT SeqNum

### ***Volume Shadow Service***
- Framework that allows NTFS volumes to be backed up without being taken offline
- This allows users to recover previous versions of files that were backed up when a system restore point was created or a backup made by Windows Backup Application

**Volume Shadow Copy**
- Volume Shadow Service works by using multiple Volume Shadow Copies
- These provide a mechanism whereby deleted data can be recovered
- Recovered files will have metadata intact unlike if recovered form unallocated clusters

*Implementation*
- When a shadow copy is created, a special differential file, is created at root of Volume
    - System Volume Information
    - Diff files look like {GUID}{GUID}
- Once created, any 16KB block on the volume that will be modified is preserved in the differential file
- When a file is deleted, only the $MFT record is changed. The actual 16KB block containing the file data would likely not be in the VSC

**Recovery Methods**

*Raw Searching of VSC Diff Files*
- This can be useful to find $MFT records, but will not likely allow you to recover a whole file
- Could help for things like resident data or ADS (Zone Identifiers)

*Reading VSC Diff Files Directly*
- Would need third party tool
- Overlays changed blocks from VSC onto existing volume

*Reading Volume Shadow Copies Using Windows*
- This technique uses the Physical Disk Emulator so Windows can interpret the VSC data
- VSS Examiner Enscript
    - Handles process of enumerating, mounting, and searching VSCs
    - Mount the Disk using PDE
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Device -> Share -> Mount as Emulated Disk
    - Create Folder on Examiner system to act as mount point
    - Get Shadow info in Command Prompt
<br>&nbsp;&nbsp;&nbsp;&nbsp;- \<vssadmin list shadows\>
    - Run VSS Examiner Enscript
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Set Root VSS folder to new folder from above
<br>&nbsp;&nbsp;&nbsp;&nbsp;-  Select proper shadows from info retrieved from vssadmin command
<br>&nbsp;&nbsp;&nbsp;&nbsp;-  Can set target conditions and add Hash for files to filter results
    - Files will show up in Evidence now

### ***Memory Analysis***

**RAM Usage**
- CPUs process input data and produce output data but cannot access disks directly, they can only
access RAM
- Virtual Memory
    - Modern Computers access memory as sequence of pages
    - Pages are swapped in and out of RAM when needed to simulate more memory
    - Pagefile.sys and hiberfil.sys
- Buffering
    - Relates to process of reading data from an input stream and placing into RAM
    - Buffer refers to the memory allocated for use by a process
    - Buffering occurs differently based on the process
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Can try to load all input into buffer before processing
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Or can just load data that is necessary right now
    - Encrypted Volume containers will often try to keep as little unencrypted data in RAM as possible
- Memory Caching
    - Similar to buffering, but refers to storing frequently accessed files in memory
    - Windows uses this for speed for certain files like
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Registry
<br>&nbsp;&nbsp;&nbsp;&nbsp;- $MFT
    - File Systems and applications define different structures for data stored in memory (OS class)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Can search for these structures to find information

**Volatility**
- \<vol.py --help\>
- \<vol.py \[plugin\] --help\>
- For Windows need to specify profile and location of KDGB or, for Win10 and 8, KdCopyDataBlock
KDGB and KdCopyDataBlock
    - KDGB is a structure maintained by Windows kernel for debugging purposes
    - There can be multiple KDGB structures in RAM
    - Windows 10 and 8 KDGB structures are encrypted and we need the KdCopyDataBlock
    - The KdCopyDataBlock allows us to decrypt the KDGB structure
    - By running kdgbscan, we can find the address for both the KDGB and KdCopyDataBlock
<br>&nbsp;&nbsp;&nbsp;&nbsp;- If there are mulitple, find the KDGB with processes listed

**Common Analysis**
- Process lists
- Open Ports
- Route Table information
- Arp information
- User - SID
- Registry Hive list
- Password Hashes
- Process Trees
- Dlls loaded
- Process injection
- Open Files

### ***Browser Artifacts***

- These are categories of browser artifacts that should generally apply to all browsers

**Configuration**
- Can tell you more about the normal usage of browser
- Look at Home Page, Startup pages, facebook, gmail, etc...
- Is the browser set to surf anonymously?
- Has a custom location been specified for downloads?

**Browsing History**
- Ability to lookup websites one has visited in past
- Often can be stored in jumplists, not just recent but frequently visited too

**Bookmarks**
- Can indicate intent to return

**Cache Content**
- Might contain actual web page or resources (videos, pictures, etc...)
- Can help determine content or nature of web page when not obvious from url

**Cookies**
- History of visited sites
- User Info for Sites - CustomerID, Email, telephone
- Last Time Visited
- Will have different information depending on site

**Web Server Data**
- Not really browser artifact
- Information about user activity likely stored in a DB on web server
- Very difficult to retrieve

**Local Storage**
- HTML 5 - introduced local storage due to limitations in cookie based storage
- Differs for every browser, but might be possible to recover

**Form Data and Web Passwords**
- Name, address, phone, email, password
- Things entered into a web form may be saved by browser
- Often encrypted, but might be recoverable

### ***Internet Explorer and Edge***

- Much of these artifacts are not just for IE but Windows Explorer as well
- Processor -> Find Internet Artifacts
    - IE - v11
    - Results will be in Artifacts -> Internet

**Bookmarks**
- Will show file path to .url files
- Last Two columns - URL Host and URL Name

**Cache**
- Encase will automatically decompress the GZIP files when processed
- Easy way to browse, set include and then Gallery
- Can also search for URLs of interest and look at html

**History**
- Typed Url
    - Normally from NTUser.dat
    - Clicked links will also appear in here
- Daily
    - From WebCacheV01.dat
    - Can set condition to search for file:// to get file history
    - Can set condition for search engines to see search history
    - Can correlate to users by viewing the path of WebCacheV01.dat in which it came 

**WebCacheV01.dat**
- AppData\Local\Microsoft\Windows\WebCache\Internet Explorer
- Similar to DBs used for Exchange and AD
- Contains "ResponseHeaders"
    - Website title shown in browser tab
    - Visit count as displayed to user
    - Last-visit dateas displayed to user

**Getting Accurate Access Count**
- Access Count Column represents the number of times the WebCacheV01.dat record was accessed not the number of time the URL was
- Select WebCacheV01.dat -> Enscript - IE Webcache Visit Decoder

**Recovering Deleted History from IE**
- Can try to raw keyword search the WebCache Folder
- Enscript - Internet Explorer WebCache Record Recovery
    - This can take a very long time 

**Microsoft Edge (Barely went over)**
- AppData\local\Packages\Microsoft.MicrosoftEdge_8xxxxxxxxxx\AC\*
- The Cache and Cookie content will be stored a couple levels done in appropriate folders
- The WebCacheV01.dat cannot be viewed in Encase
    - ESE database format
    - Need to export and view with 3rd party tool

### ***Mozilla FireFox***

**FireFox User Profiles**
- Firefox provides a feature to run the browser using different profiles.
- User data for each profile will be segregated into randomly named subfolders
- The default user settings will be in subfolder with name ending in ".default"

**Base Artifact Location**
- AppData\Roaming\Mozilla\FireFox\Profiles\\<FireFox Profile Folder\>
    - Config Settings
    - History
    - Bookmarks
    - Local Storage
    - Form data and web passwords
- AppData\Local\Mozilla\FireFox\Profiles\\<FireFox Profile Folder\>
    - Cache Content
- Most of Mozillas information is stored in SQLite files

**Pref.js**
- AppData\Roaming\Mozilla\FireFox\Profiles\\<FireFox Profile Folder\>\pref.js
- Javascript file with user preferences (Home Page, startup pages, etc.)
- Some values won't be present if value is default

**Encase Processor**
- Processor -> Find Internet Artifacts
    - Mozilla FireFox - v51.0.0
- Typically most entries come from 3 places
    - places.sqlite - Bookmarks and browsing history
    - cookies.sqlite - Cookies
    - formhistory.sqlite - Form and Search bar history
- Processor doesn’t handle associated WAL files

**Consequences of SQLite Write -Ahead-Logging (WAL)**
- SQLite DBs use fixed-size blocks called pages
- Modified Pages are not written into main DB right away instead go to WAL file (*.wal)
- Two situations may occur making us want to view DB with and without WAL
    - WAL file may contain new data not yet in main DB
<br>&nbsp;&nbsp;&nbsp;&nbsp;- For this want to View DB with WAL
    - DB may contain deleted entries that will persist until page is replaced with page in WAL
<br>&nbsp;&nbsp;&nbsp;&nbsp;- For this want to view DB without WAL

**Handling WAL**
- Export WAL and DB out of Encase and view in 3rd party tool (SQLite Expert)
- Enscript - Generic SQLite Database Parser
- Enscript - View SQLite With WAL Plugin - need an external viewer installed

**Secure Wipe**
- Firefox uses secure wipe deletion in its implementation of SQLite

### ***Google Chrome***

**Chrome User Profiles**
- Chrome provides a feature to run the browser using different profiles.
- These are often linked to Google Accounts.
- User data for each profile will be subfolders where default profile in "Default" and additional
profiles in "Profile\<n\>"

**Base Artifact Location**
- AppData\Local\Google\Chrome\User Data\\<Chrome profile folder\>
- Chrome does NOT use the Roaming user profile folder.
- Chrome also makes more use of the Windows Registry

**Preferences**
- AppData\Local\Google\Chrome\User Data\\<Chrome profile folder\>\preferences
- JSON formatted file
- Enscript - JSON viewer plugin
- "." is the JSON path separator so session.startup_urls is session\startup_urls

**Encase Processor**
- Processor -> Find Internet Artifacts
- Will be able to pull the following
    - Downloads
    - Keyword searches
    - History
    - Cookies
    - Top Sites
- Does not Retrieve Bookmarks
    - This can be done manually by viewing JSON file
    - AppData\Local\Google\Chrome\User Data\\<Chrome profile folder\>\Bookmarks
- Chrome Cache
    - Google has two locations for Chrome Cache
<br>&nbsp;&nbsp;&nbsp;&nbsp;- AppData\Local\Google\Chrome\User Data\\<Chrome profile folder\>\Cache
<br>&nbsp;&nbsp;&nbsp;&nbsp;- AppData\Local\Google\Chrome\User Data\\<Chrome profile folder\>\Media Cache
    - Most Cache is parsed automatically but content downloaded in a stream format is not
    - Sparse Cache Data
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Format that allows resources to be viewed before all data downloaded (Movies, PDFs,
etc.)
<br>&nbsp;&nbsp;&nbsp;&nbsp;- 2 ways to identify
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Sparse Cache will create multiple 1MB files in cache folder
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Cache records with HTTP Header result code of 206 and content length header
field which doesn’t match logical size
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Viewing
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Enscript - Chrome Cache Sparse Data Parser
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Enscript - Windows Quick View Plugin
