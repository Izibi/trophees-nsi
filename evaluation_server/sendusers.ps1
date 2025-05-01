$users = query user
$body = @{
    data = $users | Out-String
    password = "abc"
}
Invoke-WebRequest -Uri "https://depot.trophees-nsi.fr/evaluation_server/user_log" -Method Post -Body $body -ContentType "application/json"