<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - BiblioTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .book-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .book-cover {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            color: white;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .status-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            border-radius: 50px;
            padding: 8px 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .action-btn {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .info-item {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="text-white fw-bold">
                        <i class="bi bi-book-fill"></i> BiblioTech
                    </h1>
                    <p class="text-white-50">Modern könyvtári rendszer</p>
                </div>

                <!-- Book Card -->
                <div class="book-card p-4 position-relative">
                    <!-- Status Badge -->
                    @if($book->available_copies > 0)
                        <span class="status-badge bg-success text-white">
                            <i class="bi bi-check-circle"></i> Elérhető
                        </span>
                    @else
                        <span class="status-badge bg-danger text-white">
                            <i class="bi bi-x-circle"></i> Foglalt
                        </span>
                    @endif

                    <!-- Book Cover -->
                    <div class="book-cover">
                        <i class="bi bi-book"></i>
                    </div>

                    <!-- Book Info -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark mb-2">{{ $book->title }}</h2>
                        <h5 class="text-muted mb-3">{{ $book->author->name }}</h5>
                    </div>

                    <!-- Details -->
                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-tag-fill text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Kategória</small>
                                <strong>{{ $book->category->name }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-fill text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Kiadás éve</small>
                                <strong>{{ $book->publication_year ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bookmarks-fill text-primary me-3"></i>
                            <div>
                                <small class="text-muted d-block">Példányok</small>
                                <strong>{{ $book->available_copies }} / {{ $book->total_copies }} elérhető</strong>
                            </div>
                        </div>
                    </div>

                    @if($book->description)
                    <div class="info-item">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill text-primary me-3 mt-1"></i>
                            <div>
                                <small class="text-muted d-block">Leírás</small>
                                <p class="mb-0">{{ Str::limit($book->description, 150) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="text-center mt-4">
                        @if($book->available_copies > 0)
                            @auth
                                <a href="{{ route('borrows.create', ['book_id' => $book->id]) }}" 
                                   class="btn btn-success action-btn w-100 mb-3">
                                    <i class="bi bi-plus-circle"></i> Kölcsönzés
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="btn btn-primary action-btn w-100 mb-3">
                                    <i class="bi bi-box-arrow-in-right"></i> Bejelentkezés a kölcsönzéshez
                                </a>
                            @endauth
                        @else
                            <button class="btn btn-secondary action-btn w-100 mb-3" disabled>
                                <i class="bi bi-clock"></i> Jelenleg nem elérhető
                            </button>
                        @endif

                        <a href="{{ route('books.show', $book) }}" 
                           class="btn btn-outline-primary action-btn w-100">
                            <i class="bi bi-eye"></i> Részletek megtekintése
                        </a>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="bi bi-qr-code"></i> QR kód alapú hozzáférés
                        </small>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center mt-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-house"></i> Főoldal
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('books.index') }}" class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-search"></i> Könyvek
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
