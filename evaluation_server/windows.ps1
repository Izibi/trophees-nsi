# Example (replace with your actual API endpoint and authentication)
$apiUrl = "https://depot.trophees-nsi.fr/evaluation_server/user_data?password=9a2b3c4d5e6f7g8h9i0j"

try {
    $data = Invoke-RestMethod -Uri $apiUrl -Headers $headers -Method Get -ContentType "application/json"
} catch {
    Write-Error "Failed to retrieve user data from API: $($_.Exception.Message)"
    Exit 1
}

$users = $data.users
$groups = $data.groups

$computerName = "TropheesNSI"

$parentDirectories = @("C:\TropheesNSI", "C:\TropheesNSI\ProjetsTerritoires", "C:\TropheesNSI\ProjetsLaureats")
# Delete all rights on the parent directories
foreach ($parentDir in $parentDirectories) {
    if (Test-Path -Path $parentDir) {
        try {
            $acl = Get-Acl -Path $parentDir
            # Remove all inherited permissions
            $acl.SetAccessRuleProtection($true, $false)
            # Clear existing access rules
            $acl.Access | ForEach-Object { $acl.RemoveAccessRule($_) }
            Set-Acl -Path $parentDir -AclObject $acl
            Write-Host "Removed all permissions on '$parentDir'."
        } catch {
            Write-Error "Failed to remove permissions on '$parentDir': $($_.Exception.Message)"
        }
    } else {
        Write-Error "Parent directory '$parentDir' does not exist."
    }
}

# Ensure "Traverse Folder/Execute File" permission for parent directories
foreach ($parentDir in $parentDirectories) {
    if (Test-Path -Path $parentDir) {
        try {
            $acl = Get-Acl -Path $parentDir
            # Grant "Traverse Folder/Execute File" for "Users"
            $traverseRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Users", "Traverse", "ContainerInherit, ObjectInherit", "None", "Allow")
            $acl.AddAccessRule($traverseRule)
            Set-Acl -Path $parentDir -AclObject $acl
            Write-Host "Granted 'Traverse Folder/Execute File' permission for 'Users' on '$parentDir'."
        } catch {
            Write-Error "Failed to set 'Traverse Folder/Execute File' permission on '$parentDir': $($_.Exception.Message)"
        }
    } else {
        Write-Error "Parent directory '$parentDir' does not exist."
    }
}

# Ensure "Traverse Folder/Execute File" and full control for Administrators on parent directories
foreach ($parentDir in $parentDirectories) {
    if (Test-Path -Path $parentDir) {
        try {
            $acl = Get-Acl -Path $parentDir
            # Grant full control for "Administrators"
            $adminRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit, ObjectInherit", "None", "Allow")
            $acl.AddAccessRule($adminRule)
            Set-Acl -Path $parentDir -AclObject $acl
            Write-Host "Granted full control to 'Administrators' on '$parentDir'."
        } catch {
            Write-Error "Failed to set permissions for 'Administrators' on '$parentDir': $($_.Exception.Message)"
        }
    } else {
        Write-Error "Parent directory '$parentDir' does not exist."
    }
}

foreach ($groupInfo in $groups) {
    $groupName = $groupInfo.name
    try {
        $adsi = [ADSI]"WinNT://$computerName"
        $groupExists = $adsi.Children.Find($groupName, "Group") -ne $null
    } catch {
        $groupExists = $false
    }

    if (!$groupExists) {
        try {
            $adsi = [ADSI]"WinNT://$computerName"
            $newGroup = $adsi.Create("group", $groupName)
            $newGroup.SetInfo()
            Write-Host "Group '$groupName' created."
        } catch {
            Write-Error "Failed to create group '$groupName': $($_.Exception.Message)"
        }
    }

    $groupFolder = $groupInfo.foldername
    $groupFolderPath = "C:\TropheesNSI\$groupFolder"

    if (!(Test-Path -Path $groupFolderPath)) {
        try {
            New-Item -ItemType Directory -Path $groupFolderPath | Out-Null
            Write-Host "Folder '$groupFolderPath' created."
        } catch {
            Write-Error "Failed to create folder '$groupFolderPath': $($_.Exception.Message)"
            continue
        }
    }

    try {
        $acl = Get-Acl -Path $groupFolderPath
        # Remove all inherited permissions
        $acl.SetAccessRuleProtection($true, $false)
        # Clear existing access rules
        $acl.Access | ForEach-Object { $acl.RemoveAccessRule($_) }
        # Add permissions for the specific group
        $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("$computerName\$groupName", "Modify, FullControl", "ContainerInherit, ObjectInherit", "None", "Allow")
        $acl.AddAccessRule($accessRule)
        # Add full control for "Administrators"
        $adminRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Administrators", "FullControl", "ContainerInherit, ObjectInherit", "None", "Allow")
        $acl.AddAccessRule($adminRule)
        Set-Acl -Path $groupFolderPath -AclObject $acl
        Write-Host "Permissions set for group '$groupName' and 'Administrators' on folder '$groupFolderPath'."
    } catch {
        Write-Error "Failed to set permissions for group '$groupName' and 'Administrators' on folder '$groupFolderPath': $($_.Exception.Message)"
    }
}


foreach ($user in $users) {
    $username = $user.username
    $password = $user.password
    $groups = $user.groups

    try {
        $adsi = [ADSI]"WinNT://$computerName"
        $userExists = $adsi.Children.Find($username, "User") -ne $null
    } catch {
        $userExists = $false
    }

    if (!$userExists) {
        # Create the user account
        try {
            $user = [ADSI]"WinNT://$computerName"
            $newUser = $user.Create("user", $username)
            $newUser.SetPassword($password)
            $newUser.SetInfo()
            Write-Host "User account '$username' created."
        } catch {
            Write-Error "Failed to create user '$username': $($_.Exception.Message)"
        }
    }

    # Ensure the user is added to the "Remote Desktop Users" group
    try {
        $rdpGroup = [ADSI]"WinNT://$computerName/Remote Desktop Users,group"
        $rdpGroup.Add("WinNT://$computerName/$username")
        Write-Host "Added $username to 'Remote Desktop Users' group for RDP access."
    } catch {
    }

    # Refresh the user's group membership
    try {
        $userObj = [ADSI]"WinNT://$computerName/$username,user"
        $userObj.Invoke("SetInfo")
        Write-Host "Refreshed group membership for user '$username'."
    } catch {
        Write-Error "Failed to refresh group membership for user '$username': $($_.Exception.Message)"
    }

    try {
        $adsi = [ADSI]"WinNT://$computerName"
        foreach ($child in $adsi.Children) {
            if ($child.SchemaClassName -eq "Group") {
                $groupName = $child.Name
                try {
                    $groupObj = [ADSI]"WinNT://$computerName/$groupName,group"
                    if ($groupObj.Invoke("IsMember", ([ADSI]"WinNT://$computerName/$username").Path) -and ($groupName -notin $groups)) {
                        $groupObj.Remove("WinNT://$computerName/$username")
                        Write-Host "Removed $username from $groupName"
                    }
                } catch {
                    Write-Host "Could not remove $username from $groupName. Maybe already gone?"
                }
            }
        }
    } catch {
        Write-Error "Error getting group memberships: $($_.Exception.Message)"
    }

    foreach ($groupName in $groups) {
        try {
            $group = [ADSI]"WinNT://$computerName/$groupName,group"
        } catch {
            $group = $null
        }

        if ($group -eq $null) {
            Write-Error "Group '$groupName' could not be retrieved or created. Skipping addition of '$username' to this group."
            continue
        }

        try {
            $group.Add("WinNT://$computerName/$username")
            Write-Host "Added $username to group '$groupName'"
        } catch {
        }

        # Find the group folder path
        $groupInfo = $data.groups | Where-Object { $_.name -eq $groupName }
        if ($groupInfo) {
            $groupFolder = $groupInfo.foldername
            $groupFolderPath = "C:\TropheesNSI\$groupFolder"

            # Set permissions for the user on the group folder
            try {
                $acl = Get-Acl -Path $groupFolderPath
                # Allow the user to create files and folders
                $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("$computerName\$username", "Modify, FullControl", "ContainerInherit, ObjectInherit", "None", "Allow")
                $acl.AddAccessRule($accessRule)
                Set-Acl -Path $groupFolderPath -AclObject $acl
                Write-Host "Set 'Modify' permissions for user '$username' on folder '$groupFolderPath'."
            } catch {
                Write-Error "Failed to set 'Modify' permissions for user '$username' on folder '$groupFolderPath': $($_.Exception.Message)"
            }
        } else {
            Write-Warning "Group information not found for group '$groupName'. Could not set permissions for user '$username' on the group folder."
        }
    }
}


# Restrict "Users" from creating files and folders in parent directories
$parentDirectories = @("C:\TropheesNSI", "C:\TropheesNSI\ProjetsTerritoires", "C:\TropheesNSI\ProjetsLaureats")
foreach ($parentDir in $parentDirectories) {
    if (Test-Path -Path $parentDir) {
        try {
            $acl = Get-Acl -Path $parentDir
            # Deny "Create Files/Write Data" and "Create Folders/Append Data" for "Users"
            $denyRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Users", "CreateFiles, CreateDirectories", "ContainerInherit, ObjectInherit", "None", "Deny")
            $acl.AddAccessRule($denyRule)
            Set-Acl -Path $parentDir -AclObject $acl
            Write-Host "Restricted 'Users' from creating files and folders in '$parentDir'."
        } catch {
            Write-Error "Failed to set restrictions on '$parentDir': $($_.Exception.Message)"
        }
    } else {
        Write-Error "Parent directory '$parentDir' does not exist."
    }
}

# Restrict "Users" from creating files and folders in parent directories
$parentDirectories = @("C:\TropheesNSI", "C:\TropheesNSI\ProjetsTerritoires", "C:\TropheesNSI\ProjetsLaureats")
foreach ($parentDir in $parentDirectories) {
    if (Test-Path -Path $parentDir) {
        try {
            $acl = Get-Acl -Path $parentDir
            # Deny "Create Files/Write Data" and "Create Folders/Append Data" for "Users"
            $denyRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Users", "CreateFiles, CreateDirectories", "ContainerInherit, ObjectInherit", "None", "Deny")
            $acl.AddAccessRule($denyRule)
            Set-Acl -Path $parentDir -AclObject $acl
            Write-Host "Restricted 'Users' from creating files and folders in '$parentDir'."
        } catch {
            Write-Error "Failed to set restrictions on '$parentDir': $($_.Exception.Message)"
        }
    } else {
        Write-Error "Parent directory '$parentDir' does not exist."
    }
}

