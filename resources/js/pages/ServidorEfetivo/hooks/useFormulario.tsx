import { useForm } from "@inertiajs/react";
import { ptBR } from "date-fns/locale/pt-BR";
import { FormEvent } from "react";
import { registerLocale } from "react-datepicker";

export default function useFormulario() {

  registerLocale('pt-BR', ptBR)

  const { data, setData, post, processing } = useForm({
    pes_nome: '',
    pes_data_nascimento: '',
    pes_sexo: '',
    pes_mae: '',
    pes_pai: '',
    se_matricula: '',
  });

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    post(route('servidores.efetivo.store'));
  }

  const handleDateChange = (date: Date | null) => {
    if (date) {
      const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
      const formattedDate = localDate.toISOString().split('T')[0];
      setData('pes_data_nascimento', formattedDate);
    } else {
      setData('pes_data_nascimento', '');
    }
  }

  const getDateValue = () => {
    if (!data.pes_data_nascimento) return null;

    const [year, month, day] = data.pes_data_nascimento.split('-').map(Number);
    const date = new Date(year, month - 1, day, 12, 0, 0);

    return isNaN(date.getTime()) ? null : date;
  }

  return {
    data,
    setData,
    post: handleSubmit,
    processing,
    handleDateChange,
    getDateValue,
  }
}