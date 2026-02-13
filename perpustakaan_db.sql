-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 13, 2026 at 05:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `target_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'create_genre', 7, 'Genre created: Anak', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-02 15:19:54'),
(2, 2, 'update_genre', 3, 'Genre updated to: Sains Populer', '192.168.100.195', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-02 15:34:50'),
(3, 2, 'create_book', 8, 'Book created: Yang Jauh Tersembunyi by Sean Carroll', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 05:50:11'),
(4, 2, 'delete_book', 3, 'Book deleted: Seni Digital Kontemporer by Alex Johnson', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 05:50:31'),
(5, 2, 'create_book', 9, 'Book created: The Righteous Mind by Jonathan Haidt', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:26:28'),
(6, 2, 'create_book', 10, 'Book created: Kanker by Siddharta Mukherjee', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:33:37'),
(7, 2, 'create_book', 11, 'Book created: The Magic of Reality by Richard Dawkins', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:39:46'),
(8, 2, 'create_book', 12, 'Book created: The God Delusion by Richard Dawkins', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:44:59'),
(9, 2, 'update_book', 12, 'Book updated: The God Delusion by Richard Dawkins', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:46:53'),
(10, 2, 'create_book', 13, 'Book created: Kosmos: Aneka Ragam Dunia by Ann Druyan', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:53:31'),
(11, 2, 'create_book', 14, 'Book created: Kartun Non Komunikasi by Larry Gonick', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:58:09'),
(12, 2, 'update_book', 14, 'Book updated: Kartun Non Komunikasi by Larry Gonick', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 07:58:43'),
(13, 2, 'create_book', 15, 'Book created: The World Until Yesterday by Jared Diamond', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:17:30'),
(14, 2, 'create_book', 16, 'Book created: Gun, Germs, and Steel by Jared Diamond', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:25:56'),
(15, 2, 'create_book', 17, 'Book created: Collapse by Jared Diamond', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:29:04'),
(16, 2, 'update_book', 16, 'Book updated: Gun, Germs, and Steel by Jared Diamond', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:29:26'),
(17, 2, 'create_book', 18, 'Book created: Hingga Akhir Waktu by Brian Greene', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:35:58'),
(18, 2, 'create_book', 19, 'Book created: Fear of Physics: A Guide for the Perplexed by Lawrence M. Krauss', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:42:23'),
(19, 2, 'create_book', 20, 'Book created: Dunia Paralel by Michio Kaku', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 08:59:51'),
(20, 2, 'create_book', 21, 'Book created: The Body by Bill Bryson', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:14:00'),
(21, 2, 'create_book', 22, 'Book created: Misteri-misteri Tentang Ruang dan Waktu by Bill Bryson', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:19:37'),
(22, 2, 'create_book', 23, 'Book created: The Puppeteer by Jostein Gaarder', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:26:53'),
(23, 2, 'create_book', 24, 'Book created: The Magic Library by Jostein Gaarder', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:30:19'),
(24, 2, 'create_book', 25, 'Book created: The Castle of The Pyrenees by Jostein Gaarder', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:43:11'),
(25, 2, 'create_book', 26, 'Book created: Dunia Sophie by Jostein Gaarder', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:46:08'),
(26, 2, 'update_book', 26, 'Book updated: Dunia Sophie by Jostein Gaarder', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:48:14'),
(27, 2, 'create_book', 27, 'Book created: Dunia Anna by Jostein Gaarder', '127.0.0.1', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-03 09:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(255) NOT NULL,
  `tahun` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  `halaman` int(11) NOT NULL,
  `link_buku` varchar(500) DEFAULT NULL,
  `link_foto` varchar(500) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `judul`, `pengarang`, `tahun`, `genre_id`, `halaman`, `link_buku`, `link_foto`, `deskripsi`, `created_at`, `updated_at`) VALUES
(5, 'Black Holes and Baby Universes', 'Stephen Hawking', 1993, 3, 218, 'https://drive.google.com/file/d/1rjOH_kJ_jrTrYOVnk_WNeaDBJI84OpbL/view?usp=drive_link', 'https://ebooks.gramedia.com/ebook-covers/52283/image_highres/ID_BHBU2020MTH05BHU.jpg', 'Dalam bukunya yang fenomenal, A Brief History of Time, Stephen Hawking dengan tegas mengubah cara berpikir kita tentang fisika, jagat raya, dan realitas. Melalui buku tersebut, Stephen Hawking, sebagai fisikawan teoretis paling cemerlang sejak Einstein, telah membuka pikiran kita untuk menerima gagasan-gagasan ilmiah paling penting dewasa ini tentang kosmos.rnrnSekarang, Stephen Hawking datang lagi untuk membersitkan cahaya baru ke kawasan-kawasan paling gelap dalam ruang-waktu... dan menyingkapkan sederet kemungkinan baru dalam memahami jagat raya', '2026-02-02 15:19:02', '2026-02-02 15:19:02'),
(8, 'Yang Jauh Tersembunyi', 'Sean Carroll', 2019, 3, 304, 'https://drive.google.com/file/d/1jLVOp8kqm0KMlOk3HXHFL3yg21Cx7lBH/view?usp=sharing', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/9786024816483_Yang_Jauh_Tersembunyi_cover_17_Sep_R3-1.jpg', 'Tahukah Anda, bahwa ada banyak sekali dunia lain di luar sana? Fisika memberikan pemahaman atas dunia. Berbagai temuan dan teori baru terus memperbarui pengetahuan sains dan mengubah gambaran dunia yang disajikan fisika. Fisika kuantum menghadirkan teka-teki di gambaran itu, dengan dualitas zarah-gelombang dan prinsip ketidakpastian dari dunia klasik yang serba pasti ke dunia kuantum yang dianggap tanpa kepastian, serta segala sesuatu hanya diketahui peluangnya. Namun, fisika kuantum belum selesai. Tafsir Kopenhagen yang sekarang berlaku, dengan dualitas dan ketidakpastian tidak lagi menjadi kata pamungkas. Sejumlah ahli fisika lain mengusulkan beberapa pertanyaan, yaitu bagaimana jika yang nyata itu bukan hanya satu kemungkinan, melainkan semuanya? Bagaimana jika segala kemungkinan bisa terjadi di dunia-dunia yang tak terhitung banyaknya, dan terbentuk tiap kali terjadi peristiwa kuantum? Itulah yang dinyatakan Teori Banyak-Dunia, suatu tafsir alternatif yang berupaya mengembalikan kepastian dan kesederhanaan di fisika kuantum. rnAhli fisika teori Sean Carroll menjelaskan fisika kuantum tanpa membuatnya misterius dan sulit diterima akal dalam buku Yang Jauh Tersembunyi. Dia juga menunjukkan cara paradigma yang beda dalam fisika kuantum berpengaruh ke cara berpikir seseorang mengenai alam semesta dan kepastian. Sean Carroll menyajikan gagasannya berdasarkan teori seorang fisikawan Amerika bernama Hugh Everett. Kemudian, ia mengembangkan teori tersebut hingga terciptanya buku ini. Pada bagian akhir buku, terdapat narasi yang menjelaskan mengenai ruang dan waktu, medan kuantum, gravitasi kuantum, serta lubang hitam.', '2026-02-03 05:50:11', '2026-02-03 05:50:11'),
(9, 'The Righteous Mind', 'Jonathan Haidt', 2012, 3, 464, 'https://drive.google.com/file/d/1D9yVtvJBBE-fNmdfm8f4AFjWDnHeSkpa/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/59267/image_highres/BLK_TRM202095824.jpg', 'Mengapa orang-orang baik terpecah belah karena politik dan agama? Mengapa masyarakat makin terpolarisasi dan saling curiga, bukannya berusaha bekerja sama? Mengapa ada pandangan moral yang berbeda-beda? Ahli psikologi Jonathan Haidt meneliti asal-usul keragaman pandangan moral di antara manusia. Peran emosi dan nalar dalam memandu moral, serta alasan moral yang mendasari berbagai kubu dalam politik, termasuk dalam bahasan buku ini. Turut tercantum usul mengenai di mana kita semua bisa mendapatkan titik temu.', '2026-02-03 07:26:28', '2026-02-03 07:26:28'),
(10, 'Kanker', 'Siddharta Mukherjee', 2010, 3, 658, 'https://drive.google.com/file/d/1NC09TWm-x8lus8L0T9Hx5irVj2AkunJW/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/51473/image_highres/ID_KBP2020MTH04KB.jpg', '“Dalam menulis buku ini, saya memulainya dengan membayangkan proyek saya sebagai ‘sejarah’ kanker. Namun lama-lama terasa seolah saya bukan sedang menulis tentang sesuatu, melainkan seseorang. Subjek saya hari demi hari berubah menjadi menyerupai individu—cerminan suatu diri yang membingungkan sekaligus gila. Buku ini bukan hanya sejarah medis suatu penyakit, melainkan sesuatu yang lebih pribadi, lebih mendalam: biografi.” Kanker merupakan penyakit yang amat besar pengaruhnya, dapat mengubah hidup pasien maupun orang-orang di sekelilingnya. Kanker juga memiliki banyak wajah dan variasi; itu karena kanker bukan hanya satu penyakit, melainkan banyak penyakit dengan ciri sama: pertumbuhan sel tak terkendali. Melawan kanker seolah melawan tubuh yang berkhianat: sel-sel kita sendiri yang berubah jadi ganas dan lepas kendali. Apa sebenarnya kanker itu? Sejak kapan kanker mulai menyerang manusia? Apa penyebab kanker? Bisakah dan bagaimanakah kanker disembuhkan dan dicegah? Buku ini, ditulis seorang dokter kanker, menawarkan jawaban untuk semua pertanyaan itu.', '2026-02-03 07:33:37', '2026-02-03 07:33:37'),
(11, 'The Magic of Reality', 'Richard Dawkins', 2011, 3, 269, 'https://drive.google.com/file/d/1qnoWa05ZpWYF4NKrc6lNsOZfRt2h3NWi/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/42474/image_highres/ID_MR2018MTH05MR.jpg', '“Saya ingin menunjukkan kepada Anda bahwa dunia nyata, seperti yang dipahami lewat sains, memiliki sihirnya tersendiri--jenis yang saya sebut sihir puitis: kecantikan yang mengilhami, yang semakin ajaib karena nyata sifatnya dan karena kita bisa memahami bagaimana cara kerjanya ... Sihir realitas sungguh mengagumkan. Mengagumkan, dan nyata. Mengagumkan karena nyata.” Ada berbagai pertanyaan besar yang diajukan manusia: Terbuat dari apakah segala benda? Bagaimana alam semesta bermula? Mengapa ada banyak jenis makhluk hidup? Mengapa ada siang dan malam? Dulu manusia menjawabnya dengan mitos dan legenda, kisah serba ajaib yang berusaha menjelaskan gejala alam. Kini ada penjelasan yang tak kalah ajaibnya: sains, yang menjawab pertanyaan-pertanyaan besar dengan melihat dan meneliti realitas itu sendiri, mempelajari apa yang benar-benar nyata. Inilah buku yang menyandingkan jawaban kedua jenis penjelasan itu: sihir mitos, dan sihir realitas--sains.', '2026-02-03 07:39:46', '2026-02-03 07:39:46'),
(12, 'The God Delusion', 'Richard Dawkins', 2012, 3, 522, 'https://drive.google.com/file/d/1kMPYmepNa1cc66sFm0XPJbUAiNztEG7f/view?usp=sharing', 'https://upload.wikimedia.org/wikipedia/id/7/76/The_God_Delusion_UK.jpg', 'The God Delusion adalah sebuah buku terkenal dan kontroversial yang membahas tema-tema seputar ketuhanan dari kacamata sains (biologi). Buku yang ditulis oleh ahli biologi Inggris ini hingga kini masih menjadi rujukan penting dalam persoalan hubungan agama dan sains. Dalam buku ini Dawkins menyatakan bahwa suatu pencipta supernatural hampir bisa dipastikan tidak ada, dan bahwa kepercayaan pada suatu tuhan personal sebagai sebuah delusi (igauan/khayalan)--suatu kepercayaan yang salah dan terus-menerus bertahan di hadapan berbagai bukti kuat yang menentangnya. Selain itu, dalam karya ini, Dawkins juga mengemukakan berbagai argumen yang menentang penjelasan-penjelasan kreasionis atas kehidupan. Dawkins mengulas secara langsung serangkaian argumen yang mendukung dan menentang keyakinan pada eksistensi Tuhan (atau dewa-dewa). Dalam buku ini Dawkins berulangkali menyatakan dirinya sebagai atheis, meskipun ia juga berulangkali menyatakan bahwa dalam pengertian tertentu ia adalah seorang agnostik.', '2026-02-03 07:44:59', '2026-02-03 07:46:53'),
(13, 'Kosmos: Aneka Ragam Dunia', 'Ann Druyan', 2019, 3, 380, 'https://drive.google.com/file/d/1jGT8uxtyqQc7EKWdre4loBjAAjEBtZan/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/61049/image_highres/BLK_KARD2021311822.jpg', 'Cosmos: Possible Worlds adalah saga lanjutan petualangan besar yang diawali bersama oleh Carl Sagan dan Ann Druyan. Cosmos: A SpaceTime Odyssey Druyan yang meraih Emmy Award dan Peabody Award merupakan fenomena global, ditayangkan di 181 negara di seantero planet ini. Kini, dengan Possible Worlds, Druyan melanjutkan perjalanan menarik yang akan membawa Anda melintas 14 miliar evolusi kosmik dan berbagai rahasia alam. Inilah kisah-kisah para penanya tanpa takut yang belum pernah disampaikan, yang pencariannya—bahkan kadang dengan biaya setinggi-tingginya—memberi kita gambaran alam semesta luas yang baru kita mulai kenali. Dalam buku memukau ini, Druyan membayangkan masa depan penuh inspirasi yang kita masih bisa dapatkan di dunia ini—jika kita sadar pada waktunya untuk menggunakan sains dan teknologi canggih dengan kebijaksanaan. Siap-siap berlayar ke bintang-bintang!', '2026-02-03 07:53:31', '2026-02-03 07:53:31'),
(14, 'Kartun Non Komunikasi', 'Larry Gonick', 1993, 3, 188, 'https://drive.google.com/file/d/122bNubXe0Qc2fEZKXhrso09Ht6XnnS5b/view?usp=sharing', 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1582167896i/1725193.jpg', 'Buku yang berjudul Kartun (Non) Komunikasi ini merupakan karya dari Larry Gonick. Buku ini dapat dibaca olehsiapa saja baik dari kalangan remaja maupun orang dewasa. Fungsi pokok bahasa adalah menyampaikan gagasan. Tapi sekarang ini kita lebih sering pusing dengan bahasa. Lewat buku ini, Larry Gonick memperlihatkan problematika berbahasa sebagai alat komunikasi dengan mengungkap hubungan antara bahasa dan makna. Sinopsis “Memperbaiki ketidakpahaman membutuhkan optimasi ketaatan terhadap skema dekompleksifikasi triadik.” Apakah kalimat seperti itu membuat kita merasa bahwa “masyarakat komunikasi” zaman sekarang malah tidak komunikatif?  Lewat Kartun (Non) Komunikasi, Larry Gonick mengupas rancunya hubungan antara bahasa dan makna, yang merupakan hakikat komunikasi. Kartun Gonick yang selalu jenaka dan usil membedah berbagai topik seperti peran pokok emosi dalam komunikasi, pentingnya bahasa tubuh, makna sejati “user-friendly”, serta timbulnya inflasi bahasa yang tiada henti. Buku ini menjawab pertanyaan-pertanyaan penting seperti: Bahasa: Apa gunanya? Masuk akalkah logika, atau logiskah akal? Apakah realitas merupakan halusinasi, dan jika betul demikian, kenapa kita bisa melihatnya dari sisi lain? Pesan Kartun (Non) Komunikasi sangat jelas: Kita perlu memahami mengapa kita sering kali sulit saling mengerti.', '2026-02-03 07:58:09', '2026-02-03 07:58:43'),
(15, 'The World Until Yesterday', 'Jared Diamond', 2012, 3, 604, 'https://drive.google.com/file/d/1kEuAmT38j3_0h0YT3L4uBa_SGnZftf8P/view?usp=sharing', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/img20220927_14070082.jpg', 'Dunia modern yang kita alami sekarang baru berlangsung sebentar dalam sejarah manusia. Sebelumnya, selama jutaan tahun, manusia hidup dalam &quot;dunia kemarin&quot; yang kini masih tersisa di masyarakat-masyarakat tradisional. Setelah membahas bangkitnya peradaban dalam Guns, Germs &amp; Steel, lalu runtuhnya peradaban dalam Collapse, Jared Diamond mengajak kita menjelajahi kehidupan masyarakat masa lalu dan tradisional guna mencari pelajaran untuk masa depan. Contoh-contoh yang ditampilkan antara lain masyarakat! Kung Afrika, India Amerika, Aborigin Australia, serta berbagai suku Papua yang diakrabi Jared Diamond dalam penelitian lapangannya selama puluhan tahun di sana. Banyak hal-hal yang kita bisa pelajari dari manusia yang telah lampau. Gagasan tersebut merupakan ide awal dari sang penulis, Jared Diamond dalam menulis buku yang berjudul “The World Until Yesterday”. Sebagai manusia modern ia ingin mempelajari keunggulan praktik-praktik hidup yang masih dijalani masyarakat-masyarakat tradisional. Sebuah catatan khusus dari sang penulis, bahwa keberadaan buku ini bukan untuk meromantisasi kita akan kehidupan tradisional. Banyak praktik-praktik dari kehidupan masyarakat tradisional sudah kita buang, bahkan kita harus bersyukur dengan hal tersebut. Misalnya, menghadapi kelaparan berkala, mengabaikan atau membunuh orang lanjut usia, dan masih banyak lagi. Menurut sang penulis, mempelajari masyarakat tradisional dapat memberi saran kepada kehidupan yang lebih baik. Lebih daripada itu, mempelajari masyarakat tradisional membantu kita untuk menghargai sejumlah keunggulan masyarakat kita yang selama ini kita anggap biasa saja.', '2026-02-03 08:17:30', '2026-02-03 08:17:30'),
(16, 'Gun, Germs, and Steel', 'Jared Diamond', 1997, 3, 624, 'https://drive.google.com/file/d/1As4pWMSe9Vgsx_j4Pp1WihUU50gq3TKs/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/46035/image_highres/ID_GGS2019MTH02GGS1.jpg', 'PADA 1970-an, ketika sedang berada di Papua untuk meneliti burung, Jared Diamond ditanyai oleh sahabatnya yang orang Papua: Mengapa orang kulit putih membuat banyak barang berharga, sementara orang Papua tidak? Pertanyaan itu sebenarnya adalah pertanyaan mengenai mengapa kemajuan peradaban di berbagai benua itu berbeda-beda. Guns, Germs &amp;amp; Steel, buku pemenang Hadiah Pulitzer 1998, adalah jawaban Jared Diamond bagi pertanyaan sahabatnya. Mengapa sebagian bangsa di dunia bisa mencapai kemajuan teknologi dan peradaban, sehingga lantas menaklukkan dan menjajah bangsa di bagian dunia lain? Apakah itu karena bangsa-bangsa itu hakikatnya lebih unggul daripada lainnya? Atau semua bangsa sama saja, dan yang membedakan adalah faktor lingkungan berupa tanah, iklim, flora-fauna, dan sejarah alam? Guns, Germs &amp;amp; Steel mengajak kita melihat riwayat peradaban manusia pada masa tepat sebelum masa sejarah—mulai sekitar tahun 11000 SM—yang justru penting karena pada waktu itulah unsur-unsur pembentuk peradaban manusia seperti pertanian dan bahasa muncul. Dari situ kita diajak meninjau perkembangan di semua benua, dan mengetahui mengapa kemajuan peradaban manusia di berbagai tempat itu berbeda-beda.', '2026-02-03 08:25:56', '2026-02-03 08:29:26'),
(17, 'Collapse', 'Jared Diamond', 2005, 3, 734, 'https://drive.google.com/file/d/1qRy6MioYyjQva5vkoUYUhVtUY2rSU1Tl/view?usp=sharing', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/images/1/42857/image_highres/ID_CP2018MTH06CP.jpg', 'Ratusan peradaban telah bangkit dan runtuh sepanjang sejarah manusia. Setelah membahas bangkitnya peradaban dalam Guns, Germs &amp; Steel, Jared Diamond melanjutkan dengan membahas keruntuhan berbagai peradaban masa lalu dan masa kini dalam Collapse. Collapse menunjukkan apa arti penting aspek lingkungan bagi kelangsungan hidup kita sekarang, pelajaran dari keruntuhan maupun keberhasilan berbagai masyarakat, serta apa yang telah dan bisa dilakukan perorangan, badan usaha, dan negara untuk menemukan cara mencegah peradaban ambruk karena dunia tak kuat menanggungnya. Bagi kamu yang peduli soal lingkungan, serta hubungan timbal baliknya dengan manusia, buku yang satu ini mungkin dapat menjadi salah satu bahan bacaan kamu. Buku ini berisi bahasan yang menarik dan perspektif yang intensif soal kerusakan lingkungan sebagai pembawa bencana. Selain itu, buku ini adalah buku pelengkap dari buku sebelumnya yang berjudul Guns, Germs and Steel (Bedil, Kuman dan Baja), tentang kemunculan dan perkembangan peradaban, yang juga merupakan hasil penelitian jangka panjang yang penulis lakukan di hampir setiap belahan dunia. Meskipun dengan penggunaan bahasan nya yang begitu serius, kisah-kisah soal keruntuhan di Pulau Paskah, Suku Maya, Nors Tanah Hijau, Anasazi, dan lainnya menjadi bagian dari buku ini yang tentunya sangat menarik untuk disimak. Penjelasan saintifik yang dijabarkan masuk akal dan memberikan gambaran jelas tentang peran keberlanjutan lingkungan. Buku ini intensif membahas soal hubungan timbal balik antara masyarakat dengan lingkungan.', '2026-02-03 08:29:04', '2026-02-03 08:29:04'),
(18, 'Hingga Akhir Waktu', 'Brian Greene', 2026, 3, 494, 'https://drive.google.com/file/d/1jE2db1fxOWHf_eGa-T5eWvffPqRNE4oE/view?usp=sharing', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/Hingga_Akhir_Waktu_C_1_4_page-0001.jpg', 'Dalam keutuhan waktu, segala yang hidup akan mati. Selama tiga miliar tahun lebih, selagi spesies-spesies sederhana dan kompleks mendapat tempat di hierarki Bumi, kematian telah terus membayangi mekarnya kehidupan. Keragaman menyebar selagi kehidupan merayap dari laut ke darat, dan terbang ke angkasa. Namun tunggulah cukup lama, hingga buku catatan kelahiran dan kematian, yang isinya lebih banyak daripada jumlah bintang di galaksi, akan menjadi imbang dengan sungguh presisi. Apa yang terjadi di sembarang satu kehidupan tak bisa diprediksi. Ujung akhir sembarang kehidupan sudah pasti. Greene membawa kita memahami penciptaan awal alam semesta. Dia menelaah bagaimana kehidupan dan akal budi muncul dari kekacauan purba, dan bagaimana akal budi kita, dalam upaya memahami kegunaannya, mencari berbagai cara untuk memberi makna bagi pengalaman: dalam cerita, mitos, agama, ekspresi kreatif, sains, pencarian kebenaran, dan kerinduan kita akan yang kekal. Melalui serangkaian cerita yang menjelaskan berbagai lapis realitas yang saling berhubungan dari mekanika kuantum sampai kesadaran dan lubang hitam, Greene memberi kita gambaran lebih jernih tentang bagaimana kita terbentuk, di mana kita sekarang, dan ke mana kita menuju. Sinopsis Buku. Karya ahli fisika terkenal dan penulis buku laris The Elegant Universe dan The Fabric of the Cosmos, penjelajahan memukau atas luasnya waktu dan pencarian makna oleh umat manusia. Kosmos luar biasa luas dalam ruang dan waktu, tapi diatur oleh hukum-hukum matematis universal yang sederhana dan elegan. Pada lini masa kosmik itu, era manusia sungguh spektakuler, tapi hanya sejenak. Kita tahu bahwa suatu hari nanti kita semua akan tiada. Dan kita juga tahu bahwa alam semesta akan tiada juga. Hingga Akhir Waktu adalah pembahasan Brian Greene atas penjelajahan kosmos dan petualangan kita untuk memahaminya. Greene membawa kita melintas waktu, mulai dari pemahaman terbaik kita akan awal alam semesta, ke sedekat-dekatnya perkiraan sains atas akhirnya. Dia menelaah bagaimana kehidupan dan akal budi muncul dari kekacauan purba, dan bagaimana akal budi kita, dalam upaya memahami kefanaannya, mencari berbagai cara untuk memberi makna bagi pengalaman: dalam cerita, mitos, agama, ekspresi kreatif, sains, pencarian kebenaran, dan kerinduan kita akan yang kekal. Melalui serangkaian cerita yang menjelaskan berbagai lapis realitas yang saling berhubungan dari mekanika kuantum sampai kesadaran dan lubang hitam, Greene memberi kita gambaran lebih jernih tentang bagaimana kita terbentuk, di mana kita sekarang, dan ke mana kita menuju. Namun segala pengetahuan itu, yang muncul sesudah kemunculan kehidupan, akan sirna dengan berakhirnya kehidupan. Kita jadi sadar: selama saat singkat keberadaan kita, kita bertugas mencari makna kita sendiri. Mari kita mulai.', '2026-02-03 08:35:58', '2026-02-03 08:35:58'),
(19, 'Fear of Physics: A Guide for the Perplexed', 'Lawrence M. Krauss', 2007, 3, 309, 'https://drive.google.com/file/d/1ALvvJyPxY65DjIKv7M6eAPD_w7ZUWT_e/view?usp=sharing', 'https://antinomi.org/wp-content/uploads/2020/10/Fear-of-Physics_Antinomi.png', 'Buku ini ditujukan untuk siapa saja yang ingin mengetahui garis besar problem dalam fisika; bagaimana fisika bekerja dan apa yang menjadi tujuannya. Buku ini ingin mempertegas bahwa pengetahuan, terutama fisika, bukanlah monopoli para fisikawan semata. Semua orang harus mengerti dan menyadari bahwa fisika adalah pengetahuan umat manusia dan selayaknya dimiliki oleh seluruh umat manusia pula. “Jika diperlukan keberanian besar dengan menganggap bahwa sebuah ketapel dapat digunakan untuk membunuh raksasa, hal yang sama juga berlaku bahwa metode yang ada saat ini, manusia mampu menentukan nasib alam semesta” (Lawrence Krauss).', '2026-02-03 08:42:23', '2026-02-03 08:42:23'),
(20, 'Dunia Paralel', 'Michio Kaku', 2005, 3, 400, 'https://drive.google.com/file/d/1qUF-rkgMtRjcRFnantdbBxle8DIuWnGN/view?usp=sharing', 'https://b-assets.readersvibe.com/bookcover/63d7080045e06aeda2fa8a3c.jpg', 'Dalam Parallel Worlds, fisikawan ternama Michio Kaku mengajak pembaca menjelajahi kosmos secara mendebarkan dengan menyelami ranah misterius lubang hitam, perjalanan waktu, dan ruang multidimensi. Dengan penjelasan yang jernih dan menarik, Kaku memperkenalkan konsep revolusioner teori string dan teori-M, yang menyiratkan bahwa alam semesta kita mungkin hanyalah satu dari tak terhitung banyaknya gelembung dalam multiverse yang terus mengembang. Jika terbukti benar, teori-M berpotensi menjelaskan pertanyaan-pertanyaan mendasar tentang asal-usul alam semesta, termasuk misteri mengenai apa yang ada sebelum Dentuman Besar. Perjalanan memikat ke garis depan fisika modern ini menawarkan gambaran yang menakjubkan tentang kemungkinan-kemungkinan di luar batas persepsi kita, disajikan oleh salah satu pemikir terkemuka di bidangnya.', '2026-02-03 08:59:51', '2026-02-03 08:59:51'),
(21, 'The Body', 'Bill Bryson', 2019, 3, 476, 'https://drive.google.com/file/d/17SFwPJMU0G564OODGU8ZmLczjysTiV-S/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/64896/image_highres/BLK_TBPBP202199398.jpg', 'Bill Bryson lagi-lagi membuktikan diri sebagai kawan seperjalanan yang berharga selagi dia mengajak kita menjelajahi tubuh manusia—bagaimana fungsinya, kemampuan luar biasanya menyembuhkan diri, dan (sayangnya) cara-cara tubuh bisa gagal. Penuh fakta luar biasa (tubuh Anda membuat sejuta sel darah merah sejak Anda mulai membaca tulisan ini) dan anekdot Bryson yang menarik, The Body akan membawa Anda ke pemahaman lebih mendalam atas keajaiban kehidupan dan tubuh Anda. Dia menulis, “Kita menjalani hidup di dalam gumpalan daging hangat ini tapi menganggapnya biasa saja.” The Body akan menyembuhkan sikap abai itu dengan dosis tinggi fakta dan informasi menakjubkan yang asyik dibaca.', '2026-02-03 09:14:00', '2026-02-03 09:14:00'),
(22, 'Misteri-misteri Tentang Ruang dan Waktu', 'Bill Bryson', 2010, 3, 248, 'https://drive.google.com/file/d/1kaTIbx4g0sltn4FRT3v0DMvPl9kc2uny/view?usp=sharing', 'https://ebooks.gramedia.com/ebook-covers/30959/big_covers/GRAMEDIANA98317_B_.jpg', 'Tahukah kamu bahwa setiap atom dalam tubuh hampir pasti telah melewati beberapa bintang dan pernah menjadi bagian dari jutaan organisme dalam perjalanannya menjadi bagian dari dirimu, dan bahwa jika ukuran tubuhmu normal, kamu memiliki energi potensial yang cukup untuk meledak sedahsyat bom hidrogen? Apa yang telah terjadi dengan dinosaurus, berapa besarkah jagat raya, berapa beratkah bumi, mengapa air laut asin, apakah meteorit dapat menimpa kita, dan berapakah ukuran sebuah atom? Bill Bryson menggali misteri-misteri tentang ruang dan waktu serta bagaimana, meski terkesan mustahil, kehidupan dapat hadir di planet luar biasa yang kita tinggali, lalu membagikan temuan-temuannya kepada kita. Dalam buku yang sangat memikat ini, kamu akan berjumpa dengan beberapa ilmuwan aneh, teori-teori aneh yang menjadi perdebatan dalam waktu yang sangat lama, serta penemuan-penemuan tidak disengaja yang mengubah cara kita memahami sains. Bersiaplah membentangkan imajinasimu dan berselancarlah di jagat raya yang luas dan menakjubkan.', '2026-02-03 09:19:37', '2026-02-03 09:19:37'),
(23, 'The Puppeteer', 'Jostein Gaarder', 2016, 1, 352, 'https://drive.google.com/file/d/178dEjKVJCwbfABqFTOoAVVvKZJ9Ee0Bw/view?usp=sharing', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/9786024410247_9786024410247.jpg', 'Jakop Jacobsen namanya. Pria biasa dengan kehidupan yang biasa-biasa saja. Teman terdekatnya adalah Pelle Skrindo, bajak laut yang suka datang dan pergi sesukanya. Hobinya adalah menghadiri pemakaman, dan sahabat pena tersayangnya adalah Agnes. Kepada Agnes, dia mengisahkan berbagai pemakaman yang dia ikuti, juga kesan-kesan tentang keluarga para almarhum. The Puppeteer, karya terbaru Jostein Gaarder, mengajak pembaca merenung tentang kesendirian, pertemanan, serta tentang mencari tempat dan tujuan dalam kehidupan di dunia ini. Mengharukan dan menggugah empati.', '2026-02-03 09:26:53', '2026-02-03 09:26:53'),
(24, 'The Magic Library', 'Jostein Gaarder', 1999, 1, 282, 'https://drive.google.com/file/d/17Njm0rfDDqc7G6BywECo7ooH-Gao2UuY/view?usp=sharing', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/9789794339244_The-Magic-Library.jpg', 'Dua saudara sepupu, Berit dan Nils, tinggal di kota yang berbeda. Untuk berhubungan, kedua remaja ini membuat sebuah buku-surat yang mereka tulisi dan saling kirimkan di antara mereka. Anehnya, ada seorang wanita misterius, Bibbi Bokken, yang mengincar buku-surat itu. Bersama komplotannya, tampaknya Bibbi menjalankan sebuah rencana rahasia atas diri Berit dan Nils. Rencana itu berhubungan dengan sebuah perpustakaan ajaib dan konspirasi dunia perbukuan. Berit dan Nils tidak gentar, bahkan bertekad mengungkap misteri ini dan menemukan Perpustakaan Ajaib. Buku ini juga berisi cerita detektif, cerita misteri, perburuan harta karun, petualangan ala Lima Sekawan, Astrid Lindgren, Ibsen, Klasifikasi Desimal Dewey, Winnie the Pooh, Anne Frank, kisah cinta, korespondensi, teori sastra, teori fiksi, teori menulis, puisi, sejarah buku, drama, film perpustakaan, penerbitan, humor, konspirasi.', '2026-02-03 09:30:19', '2026-02-03 09:30:19'),
(25, 'The Castle of The Pyrenees', 'Jostein Gaarder', 2008, 1, 298, 'https://drive.google.com/file/d/16uYjAwsv6KMotk0pmb6OrV-dux8GwZ6O/view?usp=drive_link', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/THE-CASTLE-IN-THE-PYRENEES-.jpg', 'Selama lima tahun, Steinn dan Solrun hidup bersama dengan bahagia. Namun, semua berubah ketika dalam perjalanan ke pegunungan, mereka menabrak seorang nenek. Sejak kejadian itu, mereka berpisah, dan jalan hidup mereka saling menyimpang. Tiga puluh tahun kemudian, Steinn dan Solrun bertemu di balkon sebuah hotel. Hotel tempat tujuan mereka berlibur tiga puluh tahun lalu, sebelum kejadian tabrak lari itu terjadi. Apa yang sebenarnya terjadi tiga puluh tahun lalu? Benarkah mereka telah melakukan pembunuhan tak disengaja? Tetapi mengapa tak ada berita maupun tak ada yang melaporkan tentang tertabraknya seorang wanita tua? The Castle in the Pyrenees, karya Jostein Gaarder yang mempertanyakan tentang jiwa dan nurani manusia, melalui hubungan dua anak manusia. Kisah yang mengeksplorasi posisi kesadaran manusia di semesta. Bisakah sains menjelaskan semuanya, apakah ada daya tak terlihat yang mempengaruhi kehidupan kita?', '2026-02-03 09:43:11', '2026-02-03 09:43:11'),
(26, 'Dunia Sophie', 'Jostein Gaarder', 1991, 1, 614, 'https://drive.google.com/file/d/1jeCL2Ufr2zhh8srOQuxz298YuOtx2PJU/view?usp=drive_link', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/product-metas/pxn0901oe9.png', 'Sinopsis Sophie, seorang pelajar sekolah menengah berusia empat belas tahu. Suatu hari sepulang sekolah, dia mendapat sebuah surat misterius yang hanya berisikan satu pertanyaan: &amp;quot;Siapa kamu?&amp;quot; Belum habis keheranannya, pada hari yang sama dia mendapat surat lain yang bertanya: &amp;quot;Dari manakah datangnya dunia?&amp;quot; Seakan tersentak dari rutinitas hidup sehari-hari, surat-surat itu mempuat Sophie mulai mempertanyakan soal-soal mendasar yang tak pernah dipikirkannya selama ini. Dia mulai belajar filsafat.', '2026-02-03 09:46:08', '2026-02-03 09:48:14'),
(27, 'Dunia Anna', 'Jostein Gaarder', 2013, 1, 248, 'https://drive.google.com/file/d/1jd3dE_Nzu46xu0VNdHxrGkHwu9UHqJKG/view?usp=drive_link', 'https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/items/97897943384212_Dunia-Anna.jpg', 'Bumi 2082, Nova sangat terkejut saat tiba-tiba di terminal online-nya muncul surat dari nenek buyutnya, Anna. Surat yang ditulis 70 tahun lalu, tepat tanggal 12.12.12. Tepat saat nenek buyutnya berusia 16 tahun seperti Nova saat ini. Jostein Gaarder, penulis Dunia Sophie, kembali dengan Dunia Anna, sekali lagi mengajak kita berkaca. Dengan kisah yang ringan namun penuh makna, Jostein Gaarder kembali mengajak pembaca merenungkan eksistensi manusia dan semesta. Untuk kamu yang cinta atau ingin menumbuhkan rasa cinta pada lingkungan, mungkin novel yang satu ini cocok untuk kamu baca. Buku Dunia Anna sendiri berbeda dengan novel lainnya garapan Gaarder. Karena buku ini cenderung memfokuskan tema akan lingkungan di bumi, terlebih perubahan iklim dan dampak dari pemanasan global. Anna, selaku tokoh utama sekaligus bagian dari generasi saat ini, diharuskan untuk berpikir lebih jauh pada kondisi Bumi ini. Tindakan kerusakan lingkungan yang saat ini dilakukan justru mengancam generasi selanjutnya yang akan menempati Bumi. Melalui novel Dunia Anna ini, penulis mengajak para pembacanya untuk menjaga dan memelihara lingkungan sebelum terlambat. Kita perlu memperkuat wawasan moral dengan memandang ke masa depan. Dalam hal ini, Gaarder memberikan gambaran terkait keadaan di tahun 2082 mendatang dengan hilangnya negara-negara Arab yang diakibatkan terkubur oleh gurun, kekeringan, dan iklim yang mematikan.', '2026-02-03 09:51:05', '2026-02-03 09:51:05');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `nama_genre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `nama_genre`) VALUES
(7, 'Anak'),
(1, 'Fiksi'),
(2, 'Non-Fiksi'),
(3, 'Sains Populer'),
(4, 'Sejarah'),
(6, 'Seni'),
(5, 'Teknologi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `user_type`, `created_at`) VALUES
(1, 'admin', '$2y$10$YourHashedPasswordHere', 'admin', '2026-02-02 08:39:04'),
(2, 'biblioqueue', '$2y$10$nP8G9YJeXwgJGqAG1B6VJuyrCtqq5kvILxrq5XwPAu6CPqObRE4gq', 'admin', '2026-02-02 09:41:55'),
(3, 'darren', '$2y$10$UBNhk7MbkIru4VrOIEmGtO/PM8pinGI1z6WouSoNX04jD4SFKhd3W', 'user', '2026-02-02 10:10:14'),
(4, 'admin1', '$2y$10$5YbjOczsyuO3qsBFtXoHwu7sPKnzujqGYbpWNZSFL87HF/fbV8KKC', 'admin', '2026-02-02 11:41:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_genre` (`nama_genre`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
