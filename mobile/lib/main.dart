import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'login_screen.dart';

void main() {
  runApp(MinhasFinancasApp());
}

class MinhasFinancasApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Minhas Finanças',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blueAccent),
        useMaterial3: true,
        textTheme: GoogleFonts.poppinsTextTheme(),
      ),
      home: LoginScreen(), // Inicia na tela de login
      // Defina rotas aqui no futuro
      // routes: {
      //   '/dashboard': (context) => DashboardScreen(),
      // },
    );
  }
}
