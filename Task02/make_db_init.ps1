param(
    [string]$DatasetPath = "../Task02",
    [string]$OutputFile = "db_init.sql"
)

function Escape-SqlString {
    param([string]$value)
    if ([string]::IsNullOrEmpty($value)) {
        return "NULL"
    }
    return "'" + $value.Replace("'", "''") + "'"
}

function Extract-Year {
    param([string]$title)
    if ($title -match '\((\d{4})\)') {
        return $matches[1]
    }
    return "NULL"
}

Write-Host "Generating SQL script $OutputFile..."

try {
    $sqlContent = @()
    
    $sqlContent += "DROP TABLE IF EXISTS movies;"
    $sqlContent += "DROP TABLE IF EXISTS ratings;"
    $sqlContent += "DROP TABLE IF EXISTS tags;"
    $sqlContent += "DROP TABLE IF EXISTS users;"
    $sqlContent += ""
    
    $sqlContent += "CREATE TABLE movies ("
    $sqlContent += "    id INTEGER PRIMARY KEY,"
    $sqlContent += "    title TEXT NOT NULL,"
    $sqlContent += "    year INTEGER,"
    $sqlContent += "    genres TEXT"
    $sqlContent += ");"
    $sqlContent += ""
    
    $sqlContent += "CREATE TABLE ratings ("
    $sqlContent += "    id INTEGER PRIMARY KEY AUTOINCREMENT,"
    $sqlContent += "    user_id INTEGER NOT NULL,"
    $sqlContent += "    movie_id INTEGER NOT NULL,"
    $sqlContent += "    rating REAL NOT NULL,"
    $sqlContent += "    timestamp INTEGER NOT NULL"
    $sqlContent += ");"
    $sqlContent += ""
    
    $sqlContent += "CREATE TABLE tags ("
    $sqlContent += "    id INTEGER PRIMARY KEY AUTOINCREMENT,"
    $sqlContent += "    user_id INTEGER NOT NULL,"
    $sqlContent += "    movie_id INTEGER NOT NULL,"
    $sqlContent += "    tag TEXT NOT NULL,"
    $sqlContent += "    timestamp INTEGER NOT NULL"
    $sqlContent += ");"
    $sqlContent += ""
    
    $sqlContent += "CREATE TABLE users ("
    $sqlContent += "    id INTEGER PRIMARY KEY,"
    $sqlContent += "    name TEXT NOT NULL,"
    $sqlContent += "    email TEXT NOT NULL,"
    $sqlContent += "    gender TEXT NOT NULL,"
    $sqlContent += "    register_date TEXT NOT NULL,"
    $sqlContent += "    occupation TEXT NOT NULL"
    $sqlContent += ");"
    $sqlContent += ""
    
    $moviesFile = Join-Path $DatasetPath "movies.csv"
    if (Test-Path $moviesFile) {
        $sqlContent += "-- Load data from movies.csv"
        $moviesData = Import-Csv $moviesFile
        foreach ($row in $moviesData) {
            $movieId = $row.movieId
            $title = Escape-SqlString $row.title
            $genres = Escape-SqlString $row.genres
            $year = Extract-Year $row.title
            
            $sqlContent += "INSERT INTO movies (id, title, year, genres) VALUES ($movieId, $title, $year, $genres);"
        }
        $sqlContent += ""
    }
    
    $ratingsFile = Join-Path $DatasetPath "ratings.csv"
    if (Test-Path $ratingsFile) {
        $sqlContent += "-- Load data from ratings.csv"
        $ratingsData = Import-Csv $ratingsFile
        foreach ($row in $ratingsData) {
            $userId = $row.userId
            $movieId = $row.movieId
            $rating = $row.rating
            $timestamp = $row.timestamp
            
            $sqlContent += "INSERT INTO ratings (user_id, movie_id, rating, timestamp) VALUES ($userId, $movieId, $rating, $timestamp);"
        }
        $sqlContent += ""
    }
    
    $tagsFile = Join-Path $DatasetPath "tags.csv"
    if (Test-Path $tagsFile) {
        $sqlContent += "-- Load data from tags.csv"
        $tagsData = Import-Csv $tagsFile
        foreach ($row in $tagsData) {
            $userId = $row.userId
            $movieId = $row.movieId
            $tag = Escape-SqlString $row.tag
            $timestamp = $row.timestamp
            
            $sqlContent += "INSERT INTO tags (user_id, movie_id, tag, timestamp) VALUES ($userId, $movieId, $tag, $timestamp);"
        }
        $sqlContent += ""
    }
    
    $usersFile = Join-Path $DatasetPath "users.txt"
    if (Test-Path $usersFile) {
        $sqlContent += "-- Load data from users.txt"
        $usersData = Get-Content $usersFile
        foreach ($line in $usersData) {
            if ($line.Trim()) {
                $parts = $line.Split('|')
                if ($parts.Length -ge 6) {
                    $userId = $parts[0]
                    $name = Escape-SqlString $parts[1]
                    $email = Escape-SqlString $parts[2]
                    $gender = Escape-SqlString $parts[3]
                    $registerDate = Escape-SqlString $parts[4]
                    $occupation = Escape-SqlString $parts[5]
                    
                    $sqlContent += "INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES ($userId, $name, $email, $gender, $registerDate, $occupation);"
                }
            }
        }
        $sqlContent += ""
    }
    
    $sqlContent += "CREATE INDEX idx_ratings_user_id ON ratings(user_id);"
    $sqlContent += "CREATE INDEX idx_ratings_movie_id ON ratings(movie_id);"
    $sqlContent += "CREATE INDEX idx_tags_user_id ON tags(user_id);"
    $sqlContent += "CREATE INDEX idx_tags_movie_id ON tags(movie_id);"
    $sqlContent += "CREATE INDEX idx_movies_year ON movies(year);"
    
    $sqlContent | Out-File -FilePath $OutputFile -Encoding UTF8
    
    Write-Host "SQL script $OutputFile successfully created!"
    
    if (Test-Path $OutputFile) {
        $size = (Get-Item $OutputFile).Length
        Write-Host "File size: $($size.ToString('N0')) bytes"
    }
    
} catch {
    Write-Error "Error creating SQL script: $($_.Exception.Message)"
    exit 1
}
