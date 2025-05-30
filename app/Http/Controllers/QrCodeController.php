<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Könyv QR kód generálása
     */
    public function generateBookQr(Book $book)
    {
        $url = route('books.show', $book);
        
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($url);

        return Response::make($qrCode, 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * Gyors kölcsönzés QR kód alapján
     */
    public function quickBorrow(Request $request)
    {
        $bookId = $request->get('book_id');
        $book = Book::findOrFail($bookId);
        
        if ($book->available_copies > 0) {
            return redirect()->route('borrows.create', ['book_id' => $book->id]);
        }
        
        return back()->with('error', 'A könyv jelenleg nem elérhető');
    }

    /**
     * Könyv információk megjelenítése QR kód alapján (mobil felület)
     */
    public function showBookInfo(Book $book)
    {
        return view('qr.book-info', compact('book'));
    }

    /**
     * QR kód letöltése PDF formátumban
     */
    public function downloadBookQrPdf(Book $book)
    {
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate(route('qr.book.info', $book));

        // Simple text-based PDF alternative until we install a PDF library
        $content = "BiblioTech Library System\n\n";
        $content .= "Book: " . $book->title . "\n";
        $content .= "Author: " . $book->author->name . "\n";
        $content .= "QR Code URL: " . route('qr.book.info', $book) . "\n\n";
        $content .= "Scan the QR code to view book details on your mobile device.";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="book-' . $book->id . '-qr.txt"');
    }
}
