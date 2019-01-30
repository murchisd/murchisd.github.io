---
layout: post
author: Donald Murchison
category: n0tes
title: "Encase - Incident Investigation"
---

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

## ***Windows Registry and Autoruns***

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

## ***NTFS Metadata Files & $MFT Overview***

**Volume Boot Record**<br>
![(Missing) NTFS Boot Sector Diagram]({{ site.url }}/assets/notes/ii/ntfs_boot.png)

*Important Pieces*
- 3 byte jump code and Original Equipment Manufacturer ID (OEM)
- Volume Serial Number
- Size of Clusters
- Location of $MFT
- Location of $MFTMirr

*Metadata Overview*
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

## ***$MFT Record Header Details***
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

## ***Standard Information and Filename Attribute Details***

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

## ***Data Attribute Details***

**Data Attribute**
- If a file has any data it will have a Data Attribute
- Each attribute references a stream of data
- Primary Streams are unnamed - ADS will have a name
- Both can be resident or non-resident

*Non-Resident Data*

![(Missing) $Data and data runlists]({{ site.url }}/assets/notes/ii/data_diagram.png)

- Data run list will follow the above format and end with 0x00 when no more fragments
- The Run offset uses signed integer

*Resident Data*

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

## ***USN Change Journal Details***

- Update Sequence Number Change Journal
    - This is the same update sequence number in $SIA
- When any change is made to file or directory, Journal is updated with description of change and file affected
- This can be helpful in proving a user knew about the file, and discovering original file names to search for other evidence like link files

*Implementation*
- Located in the $Extend folder
- Has 2 alternate data streams
    - $J
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Contains the actual change-journal records
    - $Max
<br>&nbsp;&nbsp;&nbsp;&nbsp;- Contains information about the current state/configuration of journal

*Record Content*
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

*Procedure*
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

## ***$LogFile Details***

- Revolving transaction log for $MFT (Allows for recoverability)
- As metadata changes, both original data and changes written to $LogFile
    - This is before any changes actually written to $MFT
    - Once the $LogFile is full the system will force all writes to $MFT
    -$LogFile is continuously overwritten

*Implementation*
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

*Procedure*
- Processor
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

## ***EFS and Bitlocker VHDs***

{% highlight bash %}
donald@comp:~$ sudo apt-get update
{% endhighlight %}

