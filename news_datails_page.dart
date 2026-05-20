// import 'package:flutter/material.dart';
// import 'package:flutter_bloc/flutter_bloc.dart';
// import 'package:sy_tourism/bloc/news_details/news_details_bloc.dart';
// import 'package:sy_tourism/bloc/news_details/news_details_event.dart';
// import 'package:sy_tourism/bloc/news_details/news_details_state.dart';
// import 'package:sy_tourism/utils/app_color.dart';
// import 'package:sy_tourism/generated/l10n.dart';
// import 'package:sy_tourism/utils/language_utils.dart';
// import 'package:sy_tourism/utils/image_utils.dart';
// import 'package:sy_tourism/views/screens/image_zoom_page.dart';
// import 'package:sy_tourism/views/widgets/custom_app_bar.dart';

// class NewsDetailsPage extends StatefulWidget {
//   final int newsId;

//   const NewsDetailsPage({super.key, required this.newsId});

//   @override
//   State<NewsDetailsPage> createState() => _NewsDetailsPageState();
// }

// class _NewsDetailsPageState extends State<NewsDetailsPage> {
//   @override
//   void initState() {
//     super.initState();
//     // Load news details when page initializes
//     context.read<NewsDetailsBloc>().add(
//       NewsDetailsLoadRequested(newsId: widget.newsId),
//     );
//   }

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       backgroundColor: AppColor.white,

//       appBar: const CustomAppBar(title: "News Details"),

//       body: BlocBuilder<NewsDetailsBloc, NewsDetailsState>(
//         builder: (context, state) {
//           if (state is NewsDetailsLoading) {
//             return const Center(child: CircularProgressIndicator());
//           }

//           if (state is NewsDetailsLoaded) {
//             final news = state.news;
//             return SafeArea(
//               child: SingleChildScrollView(
//                 child: Column(
//                   crossAxisAlignment: CrossAxisAlignment.start,
//                   children: [
//                     // عنوان الخبر
//                     Padding(
//                       padding: const EdgeInsets.symmetric(horizontal: 16),
//                       child: Text(
//                         LanguageUtils.getLocalizedTextSafe(
//                           context: context,
//                           englishText: news.title,
//                           arabicText: news.titleAr,
//                         ),
//                         style: const TextStyle(
//                           fontSize: 25,
//                           fontWeight: FontWeight.bold,
//                         ),
//                       ),
//                     ),
//                     const SizedBox(height: 15),
//                     Padding(
//                       padding: const EdgeInsets.symmetric(horizontal: 16),
//                       child: Text(
//                         news.timeAgo,
//                         style: const TextStyle(
//                           fontSize: 14,
//                           color: Colors.black38,
//                         ),
//                       ),
//                     ),
//                     const SizedBox(height: 10),

//                     // صورة الخبر
//                     GestureDetector(
//                       onTap: () {
//                         // فتح الصورة الرئيسية في صفحة التكبير
//                         Navigator.push(
//                           context,
//                           MaterialPageRoute(
//                             builder:
//                                 (context) => ImageZoomPage(
//                                   imagePath:
//                                       news.mainImageUrl.isNotEmpty
//                                           ? news.mainImageUrl
//                                           : "assets/images/event.png",
//                                 ),
//                           ),
//                         );
//                       },
//                       child: Container(
//                         margin: const EdgeInsets.symmetric(
//                           horizontal: 16,
//                           vertical: 8,
//                         ),
//                         decoration: BoxDecoration(
//                           borderRadius: BorderRadius.circular(16),
//                           boxShadow: [
//                             BoxShadow(
//                               color: Colors.black.withValues(alpha: 0.25),
//                               blurRadius: 8,
//                               offset: const Offset(0, 4),
//                             ),
//                           ],
//                         ),
//                         child: ClipRRect(
//                           borderRadius: BorderRadius.circular(16),
//                           child:
//                               news.mainImageUrl.isNotEmpty
//                                   ? Image.network(
//                                     ImageUtils.getFullImageUrl(
//                                       news.mainImageUrl,
//                                     ),
//                                     width: double.infinity,
//                                     height: 230,
//                                     fit: BoxFit.cover,
//                                     errorBuilder: (context, error, stackTrace) {
//                                       return Image.asset(
//                                         "assets/images/event.png",
//                                         width: double.infinity,
//                                         height: 230,
//                                         fit: BoxFit.cover,
//                                       );
//                                     },
//                                   )
//                                   : Image.asset(
//                                     "assets/images/event.png",
//                                     width: double.infinity,
//                                     height: 230,
//                                     fit: BoxFit.cover,
//                                   ),
//                         ),
//                       ),
//                     ),

//                     const SizedBox(height: 16),

//                     // وصف الخبر
//                     Padding(
//                       padding: const EdgeInsets.symmetric(horizontal: 16),
//                       child: Text(
//                         LanguageUtils.getLocalizedTextSafe(
//                           context: context,
//                           englishText: news.content,
//                           arabicText: news.contentAr,
//                         ),
//                         style: const TextStyle(
//                           fontSize: 16,
//                           color: Colors.black87,
//                         ),
//                       ),
//                     ),

//                     const SizedBox(height: 16),

//                     // Additional images if available
//                     if (news.images != null && news.images!.isNotEmpty)
//                       Column(
//                         crossAxisAlignment: CrossAxisAlignment.start,
//                         children: [
//                           const Padding(
//                             padding: EdgeInsets.symmetric(horizontal: 16),
//                             child: Text(
//                               'صور إضافية',
//                               style: TextStyle(
//                                 fontSize: 16,
//                                 fontWeight: FontWeight.bold,
//                                 color: Colors.black87,
//                               ),
//                             ),
//                           ),
//                           const SizedBox(height: 8),
//                           SizedBox(
//                             height: 100,
//                             child: ListView.builder(
//                               scrollDirection: Axis.horizontal,
//                               padding: const EdgeInsets.symmetric(
//                                 horizontal: 16,
//                               ),
//                               itemCount: news.images!.length,
//                               itemBuilder: (context, index) {
//                                 final image = news.images![index];
//                                 return GestureDetector(
//                                   onTap: () {
//                                     // فتح الصورة الفرعية في صفحة التكبير
//                                     Navigator.push(
//                                       context,
//                                       MaterialPageRoute(
//                                         builder:
//                                             (context) => ImageZoomPage(
//                                               imagePath: image.imageUrl,
//                                             ),
//                                       ),
//                                     );
//                                   },
//                                   child: Container(
//                                     margin: const EdgeInsets.only(right: 8),
//                                     decoration: BoxDecoration(
//                                       borderRadius: BorderRadius.circular(8),
//                                       boxShadow: [
//                                         BoxShadow(
//                                           color: Colors.black.withValues(
//                                             alpha: 0.1,
//                                           ),
//                                           blurRadius: 4,
//                                           offset: const Offset(0, 2),
//                                         ),
//                                       ],
//                                     ),
//                                     child: ClipRRect(
//                                       borderRadius: BorderRadius.circular(8),
//                                       child: Stack(
//                                         children: [
//                                           Image.network(
//                                             ImageUtils.getFullImageUrl(
//                                               image.imageUrl,
//                                             ),
//                                             width: 100,
//                                             height: 100,
//                                             fit: BoxFit.cover,
//                                             errorBuilder: (
//                                               context,
//                                               error,
//                                               stackTrace,
//                                             ) {
//                                               return Container(
//                                                 width: 100,
//                                                 height: 100,
//                                                 color: Colors.grey[300],
//                                                 child: const Icon(
//                                                   Icons.image,
//                                                   color: Colors.grey,
//                                                 ),
//                                               );
//                                             },
//                                           ),
//                                           // أيقونة تكبير صغيرة
//                                           Positioned(
//                                             top: 4,
//                                             right: 4,
//                                             child: Container(
//                                               padding: const EdgeInsets.all(4),
//                                               decoration: BoxDecoration(
//                                                 color: Colors.black.withValues(
//                                                   alpha: 0.6,
//                                                 ),
//                                                 borderRadius:
//                                                     BorderRadius.circular(12),
//                                               ),
//                                               child: const Icon(
//                                                 Icons.zoom_in,
//                                                 color: Colors.white,
//                                                 size: 16,
//                                               ),
//                                             ),
//                                           ),
//                                         ],
//                                       ),
//                                     ),
//                                   ),
//                                 );
//                               },
//                             ),
//                           ),
//                         ],
//                       ),

//                     const SizedBox(height: 30),
//                   ],
//                 ),
//               ),
//             );
//           }

//           if (state is NewsDetailsError) {
//             return Center(
//               child: Column(
//                 mainAxisAlignment: MainAxisAlignment.center,
//                 children: [
//                   Text('Error: ${state.message}', textAlign: TextAlign.center),
//                   const SizedBox(height: 16),
//                   ElevatedButton(
//                     onPressed: () {
//                       context.read<NewsDetailsBloc>().add(
//                         NewsDetailsLoadRequested(newsId: widget.newsId),
//                       );
//                     },
//                     child: const Text('Retry'),
//                   ),
//                 ],
//               ),
//             );
//           }

//           return const Center(child: Text('Unable to load news details'));
//         },
//       ),
//     );
//   }
// }
