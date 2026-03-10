import 'package:flutter/material.dart';
import 'api_service.dart';

class AddTransactionScreen extends StatefulWidget {
  final String tipo; // 'receita' ou 'despesa'

  const AddTransactionScreen({Key? key, required this.tipo}) : super(key: key);

  @override
  _AddTransactionScreenState createState() => _AddTransactionScreenState();
}

class _AddTransactionScreenState extends State<AddTransactionScreen> {
  final _descricaoController = TextEditingController();
  final _valorController = TextEditingController();
  final _categoriaController = TextEditingController();
  
  bool _isLoading = false;

  void _saveTransaction() async {
    if (_descricaoController.text.isEmpty || _valorController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Preencha descrição e valor')));
      return;
    }

    setState(() => _isLoading = true);

    try {
      final success = await ApiService.addTransaction(widget.tipo, {
        'descricao': _descricaoController.text,
        'valor': double.tryParse(_valorController.text.replaceAll(',', '.')) ?? 0,
        'categoria': _categoriaController.text.isEmpty ? 'Geral' : _categoriaController.text,
        'data': DateTime.now().toString().split(' ')[0],
      });

      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('${widget.tipo} adicionada!'), backgroundColor: Colors.green));
        Navigator.pop(context); // Volta para a tela anterior e recarrega o dashboard
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Erro ao salvar.'), backgroundColor: Colors.red));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Erro de conexão: $e')));
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isReceita = widget.tipo == 'receita';
    final color = isReceita ? Colors.green : Colors.red;

    return Scaffold(
      appBar: AppBar(
        title: Text('Nova ${isReceita ? 'Receita' : 'Despesa'}'),
        backgroundColor: color,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _descricaoController,
              decoration: InputDecoration(labelText: 'Descrição', border: OutlineInputBorder()),
            ),
            SizedBox(height: 16),
            TextField(
              controller: _valorController,
              keyboardType: TextInputType.numberWithOptions(decimal: true),
              decoration: InputDecoration(labelText: 'Valor (R\$)', border: OutlineInputBorder()),
            ),
            SizedBox(height: 16),
            TextField(
              controller: _categoriaController,
              decoration: InputDecoration(labelText: 'Categoria (Ex: Salário, Alimentação)', border: OutlineInputBorder()),
            ),
            SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _saveTransaction,
                style: ElevatedButton.styleFrom(
                  backgroundColor: color,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
                ),
                child: _isLoading 
                    ? CircularProgressIndicator(color: Colors.white)
                    : Text('SALVAR', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
              ),
            )
          ],
        ),
      ),
    );
  }
}
