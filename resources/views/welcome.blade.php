@extends('layouts.app')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Leia livre</title>
</head>
<body>
    <h1>Bem vindo ao Leia livre!</h1>
    <main>
        <section class="bg-gradient-to-br from-[#F8F9FA] to-[#E8F5E8] py-20 lg:py-32">
           <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div class="text-center max-w-4xl mx-auto">
                 <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-[#004D40] mb-6 leading-tight">Discover the World's Greatest<span class="block text-[#B8860B] mt-2">Literary Treasures</span></h1>
                 <p class="text-lg md:text-xl text-gray-600 mb-12 leading-relaxed max-w-3xl mx-auto">Explore thousands of public domain books with rich historical context, detailed author biographies, and multiple download formats. Your gateway to timeless literature.</p>
                 <div class="mb-12">
                    <form class="max-w-2xl mx-auto">
                       <div class="relative">
                          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i class="ri-search-line text-gray-400 text-xl"></i></div>
                          <input placeholder="Search for books, authors, or topics..." class="block w-full pl-12 pr-32 py-4 text-lg border-2 border-gray-200 rounded-2xl placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#004D40] focus:border-transparent bg-white shadow-lg" type="text" value="">
                          <div class="absolute inset-y-0 right-0 flex items-center pr-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-6 py-3 text-lg rounded-lg  rounded-xl px-6" type="submit">Search</button></div>
                       </div>
                    </form>
                    <div class="mt-6 flex flex-wrap justify-center gap-3"><span class="text-sm text-gray-500">Popular searches:</span><button class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Shakespeare</button><button class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Jane Austen</button><button class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Classic Literature</button><button class="text-sm text-[#004D40] hover:text-[#B8860B] font-medium transition-colors cursor-pointer">Poetry</button></div>
                 </div>
                 <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#B8860B] text-white hover:bg-[#DAA520] focus:ring-2 focus:ring-[#B8860B]/20 px-6 py-3 text-lg rounded-lg  min-w-[200px]"><i class="ri-grid-line mr-2"></i>Browse Categories</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-6 py-3 text-lg rounded-lg  min-w-[200px]"><i class="ri-user-line mr-2"></i>Explore Authors</button></div>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                       <div class="text-3xl font-bold text-[#B8860B] mb-2">15,000+</div>
                       <div class="text-lg text-gray-600">Free Books</div>
                       <div class="text-sm text-gray-500 mt-1">Available for download</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                       <div class="text-3xl font-bold text-[#B8860B] mb-2">2,500+</div>
                       <div class="text-lg text-gray-600">Authors</div>
                       <div class="text-sm text-gray-500 mt-1">From around the world</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                       <div class="text-3xl font-bold text-[#B8860B] mb-2">50+</div>
                       <div class="text-lg text-gray-600">Languages</div>
                       <div class="text-sm text-gray-500 mt-1">Multiple formats</div>
                    </div>
                 </div>
              </div>
           </div>
        </section>
        <section class="py-16 bg-white">
           <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div class="text-center mb-12">
                 <h2 class="text-4xl font-bold text-[#333333] mb-4">Featured Books</h2>
                 <p class="text-xl text-gray-600 max-w-3xl mx-auto">Discover our most popular public domain books, carefully selected for their literary significance and enduring appeal.</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                    <div class="flex flex-col h-full">
                       <div class="flex gap-4 mb-4">
                          <img alt="Pride and Prejudice cover" class="w-24 h-36 object-cover rounded-lg shadow-md" src="https://readdy.ai/api/search-image?query=Pride%20and%20Prejudice%20book%20cover%20classic%20literature%20Jane%20Austen%20elegant%20vintage%20design%20with%20ornate%20typography%20and%20romantic%20period%20elements%20on%20cream%20background&amp;width=300&amp;height=450&amp;seq=1&amp;orientation=portrait">
                          <div class="flex-1">
                             <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">Pride and Prejudice</h3>
                             <p class="text-[#004D40] font-semibold mb-1">by Jane Austen</p>
                             <p class="text-gray-600 text-sm mb-2">1813 • Romance</p>
                             <div class="flex items-center gap-4 text-sm text-gray-500"><span class="flex items-center"><i class="ri-star-fill text-[#B8860B] mr-1"></i>4.8</span><span class="flex items-center"><i class="ri-download-line mr-1"></i>125.420</span></div>
                          </div>
                       </div>
                       <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">A witty and romantic novel about Elizabeth Bennet and her complex relationship with the proud Mr. Darcy. Set in Georgian England, this masterpiece explores themes of love, marriage, and social class.</p>
                       <div class="space-y-3">
                          <div class="flex items-center justify-between text-sm text-gray-500"><span>432 pages</span><span>English</span></div>
                          <div class="flex flex-wrap gap-2 mb-4"><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">PDF</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">EPUB</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">TXT</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">HTML</span></div>
                          <div class="flex space-x-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-eye-line mr-2"></i>View Details</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-download-line mr-2"></i>Download</button></div>
                       </div>
                    </div>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                    <div class="flex flex-col h-full">
                       <div class="flex gap-4 mb-4">
                          <img alt="The Adventures of Sherlock Holmes cover" class="w-24 h-36 object-cover rounded-lg shadow-md" src="https://readdy.ai/api/search-image?query=Sherlock%20Holmes%20detective%20mystery%20book%20cover%20Victorian%20London%20fog%20gaslight%20atmosphere%20with%20magnifying%20glass%20and%20pipe%20elements%20on%20dark%20background&amp;width=300&amp;height=450&amp;seq=2&amp;orientation=portrait">
                          <div class="flex-1">
                             <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">The Adventures of Sherlock Holmes</h3>
                             <p class="text-[#004D40] font-semibold mb-1">by Arthur Conan Doyle</p>
                             <p class="text-gray-600 text-sm mb-2">1892 • Mystery</p>
                             <div class="flex items-center gap-4 text-sm text-gray-500"><span class="flex items-center"><i class="ri-star-fill text-[#B8860B] mr-1"></i>4.7</span><span class="flex items-center"><i class="ri-download-line mr-1"></i>98.750</span></div>
                          </div>
                       </div>
                       <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">A collection of twelve short stories featuring the brilliant detective Sherlock Holmes and his loyal companion Dr. Watson. These tales of deduction and mystery have captured readers for over a century.</p>
                       <div class="space-y-3">
                          <div class="flex items-center justify-between text-sm text-gray-500"><span>307 pages</span><span>English</span></div>
                          <div class="flex flex-wrap gap-2 mb-4"><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">PDF</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">EPUB</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">TXT</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">MOBI</span></div>
                          <div class="flex space-x-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-eye-line mr-2"></i>View Details</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-download-line mr-2"></i>Download</button></div>
                       </div>
                    </div>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                    <div class="flex flex-col h-full">
                       <div class="flex gap-4 mb-4">
                          <img alt="Alice's Adventures in Wonderland cover" class="w-24 h-36 object-cover rounded-lg shadow-md" src="https://readdy.ai/api/search-image?query=Alice%20in%20Wonderland%20whimsical%20fantasy%20book%20cover%20with%20rabbit%20hole%20playing%20cards%20tea%20party%20elements%20colorful%20illustration%20style%20on%20light%20background&amp;width=300&amp;height=450&amp;seq=3&amp;orientation=portrait">
                          <div class="flex-1">
                             <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">Alice's Adventures in Wonderland</h3>
                             <p class="text-[#004D40] font-semibold mb-1">by Lewis Carroll</p>
                             <p class="text-gray-600 text-sm mb-2">1865 • Fantasy</p>
                             <div class="flex items-center gap-4 text-sm text-gray-500"><span class="flex items-center"><i class="ri-star-fill text-[#B8860B] mr-1"></i>4.6</span><span class="flex items-center"><i class="ri-download-line mr-1"></i>87.340</span></div>
                          </div>
                       </div>
                       <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">Follow Alice down the rabbit hole into a fantastical world filled with peculiar characters and nonsensical adventures. This beloved children's classic continues to enchant readers of all ages.</p>
                       <div class="space-y-3">
                          <div class="flex items-center justify-between text-sm text-gray-500"><span>200 pages</span><span>English</span></div>
                          <div class="flex flex-wrap gap-2 mb-4"><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">PDF</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">EPUB</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">TXT</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">HTML</span></div>
                          <div class="flex space-x-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-eye-line mr-2"></i>View Details</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-download-line mr-2"></i>Download</button></div>
                       </div>
                    </div>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                    <div class="flex flex-col h-full">
                       <div class="flex gap-4 mb-4">
                          <img alt="The Picture of Dorian Gray cover" class="w-24 h-36 object-cover rounded-lg shadow-md" src="https://readdy.ai/api/search-image?query=The%20Picture%20of%20Dorian%20Gray%20gothic%20Victorian%20book%20cover%20with%20ornate%20mirror%20portrait%20frame%20dark%20elegant%20design%20on%20burgundy%20background&amp;width=300&amp;height=450&amp;seq=4&amp;orientation=portrait">
                          <div class="flex-1">
                             <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">The Picture of Dorian Gray</h3>
                             <p class="text-[#004D40] font-semibold mb-1">by Oscar Wilde</p>
                             <p class="text-gray-600 text-sm mb-2">1890 • Gothic Fiction</p>
                             <div class="flex items-center gap-4 text-sm text-gray-500"><span class="flex items-center"><i class="ri-star-fill text-[#B8860B] mr-1"></i>4.5</span><span class="flex items-center"><i class="ri-download-line mr-1"></i>76.890</span></div>
                          </div>
                       </div>
                       <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">A philosophical novel about a young man whose portrait ages while he remains eternally youthful. Wilde's only novel explores themes of aestheticism, moral corruption, and the nature of beauty.</p>
                       <div class="space-y-3">
                          <div class="flex items-center justify-between text-sm text-gray-500"><span>254 pages</span><span>English</span></div>
                          <div class="flex flex-wrap gap-2 mb-4"><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">PDF</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">EPUB</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">TXT</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">MOBI</span></div>
                          <div class="flex space-x-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-eye-line mr-2"></i>View Details</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-download-line mr-2"></i>Download</button></div>
                       </div>
                    </div>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                    <div class="flex flex-col h-full">
                       <div class="flex gap-4 mb-4">
                          <img alt="Frankenstein cover" class="w-24 h-36 object-cover rounded-lg shadow-md" src="https://readdy.ai/api/search-image?query=Frankenstein%20gothic%20horror%20book%20cover%20with%20lightning%20laboratory%20equipment%20dark%20stormy%20atmosphere%20vintage%20scientific%20instruments%20on%20black%20background&amp;width=300&amp;height=450&amp;seq=5&amp;orientation=portrait">
                          <div class="flex-1">
                             <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">Frankenstein</h3>
                             <p class="text-[#004D40] font-semibold mb-1">by Mary Shelley</p>
                             <p class="text-gray-600 text-sm mb-2">1818 • Gothic Horror</p>
                             <div class="flex items-center gap-4 text-sm text-gray-500"><span class="flex items-center"><i class="ri-star-fill text-[#B8860B] mr-1"></i>4.4</span><span class="flex items-center"><i class="ri-download-line mr-1"></i>69.420</span></div>
                          </div>
                       </div>
                       <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">The story of Victor Frankenstein and his creation, exploring themes of scientific ambition, responsibility, and what it means to be human. Often considered the first science fiction novel.</p>
                       <div class="space-y-3">
                          <div class="flex items-center justify-between text-sm text-gray-500"><span>280 pages</span><span>English</span></div>
                          <div class="flex flex-wrap gap-2 mb-4"><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">PDF</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">EPUB</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">TXT</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">HTML</span></div>
                          <div class="flex space-x-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-eye-line mr-2"></i>View Details</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-download-line mr-2"></i>Download</button></div>
                       </div>
                    </div>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 h-full">
                    <div class="flex flex-col h-full">
                       <div class="flex gap-4 mb-4">
                          <img alt="The Great Gatsby cover" class="w-24 h-36 object-cover rounded-lg shadow-md" src="https://readdy.ai/api/search-image?query=The%20Great%20Gatsby%20art%20deco%20book%20cover%20with%20golden%20lights%20green%20light%20across%20water%201920s%20jazz%20age%20elegant%20typography%20on%20emerald%20background&amp;width=300&amp;height=450&amp;seq=6&amp;orientation=portrait">
                          <div class="flex-1">
                             <h3 class="text-xl font-bold text-[#333333] mb-2 line-clamp-2">The Great Gatsby</h3>
                             <p class="text-[#004D40] font-semibold mb-1">by F. Scott Fitzgerald</p>
                             <p class="text-gray-600 text-sm mb-2">1925 • Literary Fiction</p>
                             <div class="flex items-center gap-4 text-sm text-gray-500"><span class="flex items-center"><i class="ri-star-fill text-[#B8860B] mr-1"></i>4.3</span><span class="flex items-center"><i class="ri-download-line mr-1"></i>92.150</span></div>
                          </div>
                       </div>
                       <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-3">Set in the Jazz Age, this novel tells the story of Jay Gatsby's pursuit of the American Dream and his obsession with Daisy Buchanan. A critique of wealth and excess in 1920s America.</p>
                       <div class="space-y-3">
                          <div class="flex items-center justify-between text-sm text-gray-500"><span>180 pages</span><span>English</span></div>
                          <div class="flex flex-wrap gap-2 mb-4"><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">PDF</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">EPUB</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">TXT</span><span class="px-2 py-1 bg-[#004D40]/10 text-[#004D40] text-xs rounded-full">MOBI</span></div>
                          <div class="flex space-x-2"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-eye-line mr-2"></i>View Details</button><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md  flex-1"><i class="ri-download-line mr-2"></i>Download</button></div>
                       </div>
                    </div>
                 </div>
              </div>
              <div class="text-center"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-6 py-3 text-lg rounded-lg  "><i class="ri-arrow-right-line mr-2"></i>View All Books</button></div>
           </div>
        </section>
        <section class="py-16 bg-white">
           <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div class="text-center mb-12">
                 <h2 class="text-4xl font-bold text-[#333333] mb-4">Browse by Category</h2>
                 <p class="text-xl text-gray-600 max-w-3xl mx-auto">Discover books organized by genre and topic. From timeless classics to specialized subjects, find exactly what you're looking for.</p>
              </div>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-book-open-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Classic Literature</h3>
                    <p class="text-[#B8860B] font-medium">1.250 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-search-eye-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Mystery &amp; Detective</h3>
                    <p class="text-[#B8860B] font-medium">890 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-heart-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Romance</h3>
                    <p class="text-[#B8860B] font-medium">760 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-compass-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Adventure</h3>
                    <p class="text-[#B8860B] font-medium">650 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-rocket-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Science Fiction</h3>
                    <p class="text-[#B8860B] font-medium">420 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-lightbulb-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Philosophy</h3>
                    <p class="text-[#B8860B] font-medium">380 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-time-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">History</h3>
                    <p class="text-[#B8860B] font-medium">340 books</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#004D40]/20 transition-colors"><i class="ri-quill-pen-line text-2xl text-[#004D40]"></i></div>
                    <h3 class="text-lg font-semibold text-[#333333] mb-2 group-hover:text-[#004D40] transition-colors">Poetry</h3>
                    <p class="text-[#B8860B] font-medium">290 books</p>
                 </div>
              </div>
           </div>
        </section>
        <section class="py-16 bg-[#FDFBF6]">
           <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div class="text-center mb-12">
                 <h2 class="text-4xl font-bold text-[#333333] mb-4">Renowned Authors</h2>
                 <p class="text-xl text-gray-600 max-w-3xl mx-auto">Explore the lives and works of history's most celebrated writers, with detailed biographies and complete collections of their public domain works.</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="mb-6">
                       <img alt="Jane Austen portrait" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-lg" src="https://readdy.ai/api/search-image?query=Jane%20Austen%20portrait%20elegant%20Regency%20era%20woman%20writer%20with%20period%20dress%20and%20gentle%20expression%20classical%20painting%20style%20on%20neutral%20background&amp;width=200&amp;height=200&amp;seq=7&amp;orientation=squarish">
                       <h3 class="text-2xl font-bold text-[#333333] mb-2">Jane Austen</h3>
                       <p class="text-[#004D40] font-semibold mb-1">1775 - 1817</p>
                       <p class="text-gray-600 text-sm mb-4">British</p>
                       <p class="text-[#B8860B] font-medium text-sm">Romance, Social Commentary</p>
                    </div>
                    <p class="text-gray-600 text-sm mb-6 line-clamp-4">Jane Austen was an English novelist known for her wit, social observation, and insight into the lives of women in Georgian England. Her novels, including Pride and Prejudice and Emma, remain popular today for their sharp social commentary and memorable characters.</p>
                    <div class="grid grid-cols-2 gap-4 mb-6 text-center">
                       <div class="bg-[#004D40]/5 rounded-lg p-3">
                          <div class="text-2xl font-bold text-[#004D40] mb-1">6</div>
                          <div class="text-xs text-gray-600">Books</div>
                       </div>
                       <div class="bg-[#B8860B]/5 rounded-lg p-3">
                          <div class="text-2xl font-bold text-[#B8860B] mb-1">450K</div>
                          <div class="text-xs text-gray-600">Downloads</div>
                       </div>
                    </div>
                    <a href="/preview/d65ebc38-95d6-4d7b-a7aa-349df5f24a72/3652422/authors/1" data-discover="true"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md w-full "><i class="ri-book-open-line mr-2"></i>View Works</button></a>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="mb-6">
                       <img alt="Charles Dickens portrait" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-lg" src="https://readdy.ai/api/search-image?query=Charles%20Dickens%20Victorian%20era%20author%20portrait%20with%20beard%20and%20formal%20attire%20serious%20expression%20classical%20photography%20style%20on%20sepia%20background&amp;width=200&amp;height=200&amp;seq=8&amp;orientation=squarish">
                       <h3 class="text-2xl font-bold text-[#333333] mb-2">Charles Dickens</h3>
                       <p class="text-[#004D40] font-semibold mb-1">1812 - 1870</p>
                       <p class="text-gray-600 text-sm mb-4">British</p>
                       <p class="text-[#B8860B] font-medium text-sm">Social Realism, Historical Fiction</p>
                    </div>
                    <p class="text-gray-600 text-sm mb-6 line-clamp-4">Charles Dickens was a prolific English writer and social critic who created some of the world's best-known fictional characters. His novels, including Oliver Twist and A Christmas Carol, often highlighted the plight of the poor and working class in Victorian England.</p>
                    <div class="grid grid-cols-2 gap-4 mb-6 text-center">
                       <div class="bg-[#004D40]/5 rounded-lg p-3">
                          <div class="text-2xl font-bold text-[#004D40] mb-1">15</div>
                          <div class="text-xs text-gray-600">Books</div>
                       </div>
                       <div class="bg-[#B8860B]/5 rounded-lg p-3">
                          <div class="text-2xl font-bold text-[#B8860B] mb-1">380K</div>
                          <div class="text-xs text-gray-600">Downloads</div>
                       </div>
                    </div>
                    <a href="/preview/d65ebc38-95d6-4d7b-a7aa-349df5f24a72/3652422/authors/2" data-discover="true"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md w-full "><i class="ri-book-open-line mr-2"></i>View Works</button></a>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6 hover:shadow-lg transition-shadow duration-300 text-center">
                    <div class="mb-6">
                       <img alt="Mark Twain portrait" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-lg" src="https://readdy.ai/api/search-image?query=Mark%20Twain%20American%20author%20portrait%20with%20white%20mustache%20and%20hair%20wise%20expression%2019th%20century%20photography%20style%20on%20vintage%20background&amp;width=200&amp;height=200&amp;seq=9&amp;orientation=squarish">
                       <h3 class="text-2xl font-bold text-[#333333] mb-2">Mark Twain</h3>
                       <p class="text-[#004D40] font-semibold mb-1">1835 - 1910</p>
                       <p class="text-gray-600 text-sm mb-4">American</p>
                       <p class="text-[#B8860B] font-medium text-sm">Satire, Adventure Fiction</p>
                    </div>
                    <p class="text-gray-600 text-sm mb-6 line-clamp-4">Mark Twain, born Samuel Clemens, was an American writer, humorist, and lecturer. Known for his wit and satire, he wrote classics like The Adventures of Tom Sawyer and Adventures of Huckleberry Finn, which are considered among the greatest American novels.</p>
                    <div class="grid grid-cols-2 gap-4 mb-6 text-center">
                       <div class="bg-[#004D40]/5 rounded-lg p-3">
                          <div class="text-2xl font-bold text-[#004D40] mb-1">12</div>
                          <div class="text-xs text-gray-600">Books</div>
                       </div>
                       <div class="bg-[#B8860B]/5 rounded-lg p-3">
                          <div class="text-2xl font-bold text-[#B8860B] mb-1">320K</div>
                          <div class="text-xs text-gray-600">Downloads</div>
                       </div>
                    </div>
                    <a href="/preview/d65ebc38-95d6-4d7b-a7aa-349df5f24a72/3652422/authors/3" data-discover="true"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#004D40] text-white hover:bg-[#00695C] focus:ring-2 focus:ring-[#004D40]/20 px-3 py-1.5 text-sm rounded-md w-full "><i class="ri-book-open-line mr-2"></i>View Works</button></a>
                 </div>
              </div>
              <div class="text-center"><a href="/preview/d65ebc38-95d6-4d7b-a7aa-349df5f24a72/3652422/authors" data-discover="true"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap border-2 border-[#004D40] text-[#004D40] hover:bg-[#004D40] hover:text-white px-6 py-3 text-lg rounded-lg  "><i class="ri-team-line mr-2"></i>Browse All Authors</button></a></div>
           </div>
        </section>
        <section class="py-16 bg-[#FDFBF6]">
           <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div class="text-center mb-12">
                 <h2 class="text-4xl font-bold text-[#333333] mb-4">Why Choose LibreDocs?</h2>
                 <p class="text-xl text-gray-600 max-w-3xl mx-auto">We're more than just a digital library. We provide rich context, detailed information, and a seamless reading experience for literature enthusiasts.</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6  text-center group hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300"><i class="ri-download-cloud-line text-2xl text-[#004D40] group-hover:text-white"></i></div>
                    <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Multiple Formats</h3>
                    <p class="text-gray-600 leading-relaxed">Download books in PDF, EPUB, TXT, HTML, and MOBI formats. Choose the format that works best for your reading device and preferences.</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6  text-center group hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300"><i class="ri-user-line text-2xl text-[#004D40] group-hover:text-white"></i></div>
                    <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Rich Author Information</h3>
                    <p class="text-gray-600 leading-relaxed">Discover detailed biographies, historical context, and complete bibliographies for thousands of authors from around the world.</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6  text-center group hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300"><i class="ri-search-line text-2xl text-[#004D40] group-hover:text-white"></i></div>
                    <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Advanced Search</h3>
                    <p class="text-gray-600 leading-relaxed">Find books by title, author, genre, publication year, or even specific themes. Our powerful search helps you discover new favorites.</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6  text-center group hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300"><i class="ri-bookmark-line text-2xl text-[#004D40] group-hover:text-white"></i></div>
                    <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Personal Library</h3>
                    <p class="text-gray-600 leading-relaxed">Create your own digital library, bookmark favorites, and track your reading progress across all your devices.</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6  text-center group hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300"><i class="ri-global-line text-2xl text-[#004D40] group-hover:text-white"></i></div>
                    <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Multilingual Collection</h3>
                    <p class="text-gray-600 leading-relaxed">Access books in over 50 languages, from English classics to works in French, German, Spanish, Italian, and many more.</p>
                 </div>
                 <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6  text-center group hover:shadow-lg transition-all duration-300">
                    <div class="w-16 h-16 bg-[#004D40]/10 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-[#004D40] group-hover:text-white transition-all duration-300"><i class="ri-shield-check-line text-2xl text-[#004D40] group-hover:text-white"></i></div>
                    <h3 class="text-xl font-bold text-[#333333] mb-4 group-hover:text-[#004D40] transition-colors">Completely Free</h3>
                    <p class="text-gray-600 leading-relaxed">All content is in the public domain and completely free to download, share, and use. No registration required, no hidden fees.</p>
                 </div>
              </div>
           </div>
        </section>
        <section class="py-16 bg-[#004D40]">
           <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
              <div class="mb-8">
                 <i class="ri-mail-line text-5xl text-[#B8860B] mb-6"></i>
                 <h2 class="text-4xl font-bold text-white mb-4">Stay Updated</h2>
                 <p class="text-xl text-gray-300 leading-relaxed">Get notified about new book additions, featured collections, and literary discoveries. Join thousands of book lovers in our community.</p>
              </div>
              <form class="max-w-md mx-auto" data-readdy-form="true" id="newsletter-subscription">
                 <div class="flex flex-col sm:flex-row gap-4"><input placeholder="Enter your email address" required="" class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#B8860B] focus:border-transparent text-[#333333]" type="email" value="" name="email"><button class="inline-flex items-center justify-center font-medium transition-colors duration-200 cursor-pointer whitespace-nowrap bg-[#B8860B] text-white hover:bg-[#DAA520] focus:ring-2 focus:ring-[#B8860B]/20 px-6 py-3 text-lg rounded-lg  whitespace-nowrap" type="submit"><i class="ri-send-plane-line mr-2"></i>Subscribe</button></div>
                 <p class="text-gray-400 text-sm mt-4">We respect your privacy. Unsubscribe at any time.</p>
              </form>
           </div>
        </section>
     </main></body>
</html>