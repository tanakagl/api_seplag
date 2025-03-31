import { useForm } from '@inertiajs/react'
import { ptBR } from 'date-fns/locale/pt-BR'
import { registerLocale } from 'react-datepicker'

export default function useFormulario() {
  registerLocale('pt-BR', ptBR)
  
  const { data, setData, post, processing, errors } = useForm({
    pes_nome: '',
    pes_data_nascimento: '',
    pes_sexo: '',
    pes_mae: '',
    pes_pai: '',
    st_data_admissao: '',
    st_data_demissao: '',
  })

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

  const handleAdmissaoDateChange = (date: Date | null) => {
    if (date) {
      const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
      const formattedDate = localDate.toISOString().split('T')[0];
      setData('st_data_admissao', formattedDate);
    } else {
      setData('st_data_admissao', '');
    }  
  }

  const getAdmissaoDateValue = () => {
    if (!data.st_data_admissao) return null;

    const [year, month, day] = data.st_data_admissao.split('-').map(Number);
    const date = new Date(year, month - 1, day, 12, 0, 0);

    return isNaN(date.getTime()) ? null : date;
  }

  const handleDemissaoDateChange = (date: Date | null) => {
    if (date) {
      const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
      const formattedDate = localDate.toISOString().split('T')[0];
      setData('st_data_demissao', formattedDate);
    } else {
      setData('st_data_demissao', '');
    }
  }

  const getDemissaoDateValue = () => {
    if (!data.st_data_demissao) return null;

    const [year, month, day] = data.st_data_demissao.split('-').map(Number);
    const date = new Date(year, month - 1, day, 12, 0, 0);

    return isNaN(date.getTime()) ? null : date;
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('servidores.temporario.store'))
  }

  return {
    data,
    setData,
    post: handleSubmit,
    processing,
    errors,
    handleDateChange,
    getDateValue,
    handleAdmissaoDateChange,
    getAdmissaoDateValue,
    handleDemissaoDateChange,
    getDemissaoDateValue,
  }
}
